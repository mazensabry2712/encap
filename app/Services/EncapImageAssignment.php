<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

final class EncapImageAssignment
{
    /**
     * Copy a library image into a unique file for one exact content slot.
     * The original library asset remains unchanged and can be assigned elsewhere.
     */
    public function duplicate(string $sourcePath, string $slot, string $errorField = 'image'): string
    {
        $sourcePath = ltrim(str_replace('\\', '/', $sourcePath), '/');
        $safePath = preg_match('/\\A[a-zA-Z0-9][a-zA-Z0-9_\\/.-]*\\z/', $sourcePath) === 1
            && ! collect(explode('/', $sourcePath))->contains(fn ($segment) => in_array($segment, ['', '.', '..'], true))
            && in_array(strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif'], true);

        if (! $safePath) {
            throw ValidationException::withMessages([
                $errorField => __('The selected library image is not valid. Please choose it again.'),
            ]);
        }

        $sourceDisk = null;
        $remoteBody = null;

        foreach (['encap_images', 'public'] as $diskName) {
            try {
                $candidate = Storage::disk($diskName);

                if ($candidate->exists($sourcePath)) {
                    $sourceDisk = $candidate;
                    break;
                }
            } catch (Throwable) {
                // Try the next configured disk.
            }
        }

        // Curated stock photos can still be external fallbacks before the
        // storage-preparation job downloads them. Copy them locally too.
        if ($sourceDisk === null) {
            $fallbackUrl = app(EncapImageLibrary::class)->catalogue()[$sourcePath] ?? null;

            if (is_string($fallbackUrl)) {
                try {
                    $response = Http::connectTimeout(3)->timeout(15)->get($fallbackUrl);
                    $contentType = strtolower((string) $response->header('Content-Type'));

                    if ($response->successful() && str_starts_with($contentType, 'image/')) {
                        $remoteBody = $response->body();
                    }
                } catch (Throwable) {
                    // Report the same clear selection error below.
                }
            }
        }

        if ($sourceDisk === null && $remoteBody === null) {
            throw ValidationException::withMessages([
                $errorField => __('The selected library image is no longer available. Please choose again.'),
            ]);
        }

        $extension = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
        $directory = 'assigned/'.(Str::slug($slot) ?: 'site-image');
        $targetPath = $directory.'/'.Str::uuid().'.'.$extension;

        foreach (['encap_images', 'public'] as $diskName) {
            $stream = null;
            $targetDisk = null;

            try {
                $targetDisk = Storage::disk($diskName);
                $directoryPath = $targetDisk->path($directory);

                if (! is_dir($directoryPath)
                    && ! @mkdir($directoryPath, 0755, true)
                    && ! is_dir($directoryPath)) {
                    continue;
                }

                if (! is_writable($directoryPath)) {
                    continue;
                }

                if ($remoteBody !== null) {
                    $saved = $targetDisk->put($targetPath, $remoteBody);
                } else {
                    $stream = $sourceDisk->readStream($sourcePath);

                    if (! is_resource($stream)) {
                        continue;
                    }

                    $saved = $targetDisk->put($targetPath, $stream);
                }

                if ($saved && $targetDisk->exists($targetPath)) {
                    return $targetPath;
                }

                $targetDisk->delete($targetPath);
            } catch (Throwable) {
                if ($targetDisk !== null) {
                    try {
                        $targetDisk->delete($targetPath);
                    } catch (Throwable) {
                        // Keep trying the next configured disk.
                    }
                }
            } finally {
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }
        }

        throw ValidationException::withMessages([
            $errorField => __('The image could not be saved. Please check the image-folder permissions.'),
        ]);
    }
}

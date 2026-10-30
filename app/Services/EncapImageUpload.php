<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

final class EncapImageUpload
{
    /**
     * @return array{path: string, disk: string}
     */
    public function store(UploadedFile $file, string $directory, string $errorField = 'image'): array
    {
        return $this->persist($file, $directory, null, $errorField);
    }

    /**
     * Save an upload using a caller-supplied filename while retaining the same
     * directory checks, retry policy and fallback disk as ordinary uploads.
     *
     * @return array{path: string, disk: string}
     */
    public function storeAs(
        UploadedFile $file,
        string $directory,
        string $name,
        string $errorField = 'image'
    ): array {
        if ($name === '' || $name === '.' || $name === '..'
            || basename($name) !== $name
            || str_contains($name, '/') || str_contains($name, '\\')) {
            throw new InvalidArgumentException('The upload filename must be a plain filename.');
        }

        return $this->persist($file, $directory, $name, $errorField);
    }

    /**
     * @return array{path: string, disk: string}
     */
    private function persist(
        UploadedFile $file,
        string $directory,
        ?string $name,
        string $errorField
    ): array {
        foreach (['encap_images', 'public'] as $diskName) {
            $disk = null;
            $path = null;

            try {
                // Disk construction itself can fail if the configured root is unavailable.
                $disk = Storage::disk($diskName);
                $directoryPath = $disk->path($directory);

                if (! is_dir($directoryPath)
                    && ! @mkdir($directoryPath, 0755, true)
                    && ! is_dir($directoryPath)) {
                    throw new RuntimeException('The upload directory could not be created.');
                }

                if (! is_writable($directoryPath)) {
                    throw new RuntimeException('The upload directory is not writable by PHP.');
                }

                $lastFailure = null;

                // A short retry handles transient first-write failures before falling
                // back to the legacy public disk; the result is always verified.
                for ($attempt = 1; $attempt <= 2; $attempt++) {
                    try {
                        $path = $name === null
                            ? $file->store($directory, $diskName)
                            : $file->storeAs($directory, $name, $diskName);

                        if (! is_string($path) || $path === '' || ! $disk->exists($path)) {
                            throw new RuntimeException('The host did not confirm that the uploaded file was saved.');
                        }

                        return ['path' => $path, 'disk' => $diskName];
                    } catch (Throwable $exception) {
                        $lastFailure = $exception;
                        $this->deleteQuietly($disk, $path);
                        $path = null;

                        if ($attempt < 2) {
                            clearstatcache(true, $directoryPath);
                            usleep(100000);
                        }
                    }
                }

                throw $lastFailure ?? new RuntimeException('The upload could not be verified.');
            } catch (Throwable $exception) {
                if ($disk !== null) {
                    $this->deleteQuietly($disk, $path);
                }

                logger()->warning('ENCAP image upload storage attempt failed.', [
                    'disk' => $diskName,
                    'directory' => $directory,
                    'reason' => $exception->getMessage(),
                ]);
            }
        }

        throw ValidationException::withMessages([
            $errorField => __('The image could not be saved because the hosting storage could not write the file. Please check the image-folder permissions.'),
        ]);
    }

    private function deleteQuietly($disk, mixed $path): void
    {
        if (! is_string($path) || $path === '') {
            return;
        }

        try {
            $disk->delete($path);
        } catch (Throwable) {
            // Continue to the next configured disk if cleanup on this disk fails.
        }
    }
}

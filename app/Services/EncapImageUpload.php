<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

final class EncapImageUpload
{
    /**
     * @return array{path: string, disk: string}
     */
    public function store(UploadedFile $file, string $directory, string $errorField = 'image'): array
    {
        foreach (['encap_images', 'public'] as $diskName) {
            $disk = Storage::disk($diskName);
            $path = null;

            try {
                $directoryPath = $disk->path($directory);

                if (! is_dir($directoryPath)
                    && ! @mkdir($directoryPath, 0755, true)
                    && ! is_dir($directoryPath)) {
                    throw new RuntimeException('The upload directory could not be created.');
                }

                if (! is_writable($directoryPath)) {
                    throw new RuntimeException('The upload directory is not writable by PHP.');
                }

                $path = $file->store($directory, $diskName);

                if (! is_string($path) || $path === '' || ! $disk->exists($path)) {
                    $this->deleteQuietly($disk, $path);
                    throw new RuntimeException('The host did not confirm that the uploaded file was saved.');
                }

                return ['path' => $path, 'disk' => $diskName];
            } catch (Throwable $exception) {
                $this->deleteQuietly($disk, $path);

                logger()->warning('ENCAP image upload storage attempt failed.', [
                    'disk' => $diskName,
                    'directory' => $directory,
                    'reason' => $exception->getMessage(),
                ]);
            }
        }

        throw ValidationException::withMessages([
            $errorField => __('The image could not be saved. Please try a JPG, PNG, WebP or AVIF file up to 5 MB. If this continues, the hosting image-folder permissions need checking.'),
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

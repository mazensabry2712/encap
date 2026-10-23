<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Throwable;

final class EncapImage
{
    /**
     * Resolve an image URL and keep legacy/fallback uploads reachable.
     */
    public static function url(string $path, ?string $fallbackUrl = null): string
    {
        $path = ltrim($path, '/');

        try {
            $images = Storage::disk('encap_images');

            if ($images->exists($path)) {
                return $images->url($path);
            }
        } catch (Throwable) {
            // Keep the public disk and configured fallback URL available if this disk is broken.
        }

        try {
            if (Storage::disk('public')->exists($path)) {
                return self::encapUrl($path);
            }
        } catch (Throwable) {
            // Use the supplied fallback URL when neither disk can be inspected.
        }

        return $fallbackUrl ?? self::encapUrl($path);
    }

    private static function encapUrl(string $path): string
    {
        $baseUrl = config('filesystems.disks.encap_images.url');

        if (! is_string($baseUrl) || $baseUrl === '') {
            $baseUrl = url('/encap-img');
        }

        return rtrim($baseUrl, '/').'/'.$path;
    }
}

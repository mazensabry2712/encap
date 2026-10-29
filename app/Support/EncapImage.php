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
                return self::versionedUrl($images->url($path), $images, $path);
            }
        } catch (Throwable) {
            // Keep the public disk and configured fallback URL available if this disk is broken.
        }

        try {
            $legacy = Storage::disk('public');
            if ($legacy->exists($path)) {
                return self::versionedUrl(self::encapUrl($path), $legacy, $path);
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
    /**
     * Give replaced assets a new URL so browsers cannot keep showing stale bytes.
     */
    private static function versionedUrl(string $url, $disk, string $path): string
    {
        try {
            $absolutePath = $disk->path($path);
            clearstatcache(true, $absolutePath);
            $version = $disk->lastModified($path).'-'.$disk->size($path);

            return $url.(str_contains($url, '?') ? '&' : '?').'v='.rawurlencode((string) $version);
        } catch (Throwable) {
            return $url;
        }
    }

}

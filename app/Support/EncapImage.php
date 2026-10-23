<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

final class EncapImage
{
    /**
     * Resolve an image URL, preferring the external-to-public image directory.
     */
    public static function url(string $path, ?string $fallbackUrl = null): string
    {
        $path = ltrim($path, '/');

        if (Storage::disk('encap_images')->exists($path)) {
            return Storage::disk('encap_images')->url($path);
        }

        // During deployment, keep current uploads visible until the one-time copy finishes.
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        return $fallbackUrl ?? Storage::disk('encap_images')->url($path);
    }
}

<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

final class EncapImage
{
    /**
     * Resolve an image URL, keeping legacy dashboard uploads reachable.
     */
    public static function url(string $path, ?string $fallbackUrl = null): string
    {
        $path = ltrim($path, '/');
        $images = Storage::disk('encap_images');

        // Use the Laravel-served image path for both current and legacy uploads.
        // The controller falls back to storage/app/public while migration is pending.
        if ($images->exists($path) || Storage::disk('public')->exists($path)) {
            return $images->url($path);
        }

        return $fallbackUrl ?? $images->url($path);
    }
}

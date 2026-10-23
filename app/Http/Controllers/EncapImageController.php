<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class EncapImageController extends Controller
{
    /**
     * Serve approved image files from encap-img, with a legacy upload fallback.
     */
    public function show(string $path): BinaryFileResponse
    {
        abort_unless(
            preg_match('/\A[a-zA-Z0-9][a-zA-Z0-9_\/.-]*\z/', $path) === 1
            && collect(explode('/', $path))->every(
                fn (string $segment): bool => ! in_array($segment, ['', '.', '..'], true)
            )
            && in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), [
                'jpg', 'jpeg', 'png', 'webp', 'avif', 'gif', 'ico',
            ], true),
            404
        );

        $disk = Storage::disk('encap_images');

        // Older dashboard uploads may still exist under storage/app/public.
        // Serve them through this controller too, so no public/storage symlink is required.
        if (! $disk->exists($path)) {
            $disk = Storage::disk('public');
        }

        abort_unless($disk->exists($path), 404);

        return response()->file($disk->path($path), [
            'Cache-Control' => 'public, max-age=86400',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}

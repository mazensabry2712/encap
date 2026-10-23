<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

final class EncapImageController extends Controller
{
    /**
     * Serve approved image files from encap-img or Laravel's fallback upload disk.
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

        $imageDisk = null;

        foreach (['encap_images', 'public'] as $diskName) {
            try {
                $candidate = Storage::disk($diskName);

                if ($candidate->exists($path)) {
                    $imageDisk = $candidate;
                    break;
                }
            } catch (Throwable) {
                // The other disk may still contain the requested image.
            }
        }

        abort_unless($imageDisk !== null, 404);

        try {
            $absolutePath = $imageDisk->path($path);
        } catch (Throwable) {
            abort(404);
        }

        abort_unless(is_file($absolutePath), 404);

        return response()->file($absolutePath, [
            'Cache-Control' => 'public, max-age=86400',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}

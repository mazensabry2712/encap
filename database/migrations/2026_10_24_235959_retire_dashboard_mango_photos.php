<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        $mango = DB::table('categories')
            ->where('slug', 'mango')
            ->first(['id', 'image', 'gallery']);

        if (! $mango) {
            return;
        }

        $retiredPaths = array_merge(
            is_string($mango->image) && $mango->image !== '' ? [$mango->image] : [],
            $this->decodeGallery($mango->gallery)
        );

        DB::table('categories')
            ->where('id', $mango->id)
            ->update([
                'image' => null,
                'gallery' => json_encode([], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
                'updated_at' => now(),
            ]);

        // Keep any file still referenced by another category; remove only retired
        // dashboard uploads associated exclusively with the mango category.
        $stillReferenced = [];

        foreach (DB::table('categories')->get(['image', 'gallery']) as $category) {
            if (is_string($category->image) && $category->image !== '') {
                $stillReferenced[] = $category->image;
            }

            $stillReferenced = array_merge(
                $stillReferenced,
                $this->decodeGallery($category->gallery)
            );
        }

        foreach (array_unique($retiredPaths) as $path) {
            if (! is_string($path)
                || $path === ''
                || in_array($path, $stillReferenced, true)
                || str_starts_with($path, '/')
                || in_array('..', explode('/', $path), true)
                || ! str_starts_with($path, 'categories/')) {
                continue;
            }

            foreach (['encap_images', 'public'] as $diskName) {
                try {
                    Storage::disk($diskName)->delete($path);
                } catch (\Throwable) {
                    // A missing or inaccessible disk must not interrupt the DB cleanup.
                }
            }
        }
    }

    /**
     * @return list<string>
     */
    private function decodeGallery(mixed $gallery): array
    {
        if (is_array($gallery)) {
            return array_values(array_filter($gallery, 'is_string'));
        }

        if (! is_string($gallery) || trim($gallery) === '') {
            return [];
        }

        $decoded = json_decode($gallery, true);

        return is_array($decoded)
            ? array_values(array_filter($decoded, 'is_string'))
            : [];
    }

    public function down(): void
    {
        // Intentionally irreversible: the retired dashboard image files are deleted.
    }
};

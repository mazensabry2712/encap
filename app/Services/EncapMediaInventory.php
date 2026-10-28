<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Company;
use App\Support\EncapImage;
use Illuminate\Support\Facades\Storage;

final class EncapMediaInventory
{
    public function paths(): array
    {
        $catalogue = app(EncapImageLibrary::class)->catalogue();
        $paths = array_merge(
            ['logo.png', 'favicon.png'],
            array_keys($catalogue),
            Storage::disk('encap_images')->allFiles(),
            Storage::disk('public')->allFiles(),
            Category::query()->whereNotNull('image')->pluck('image')->all(),
            Company::query()->whereNotNull('image')->pluck('image')->all(),
            Category::query()->get()->flatMap(fn (Category $item) => $item->gallery ?? [])->all(),
            Company::query()->get()->flatMap(fn (Company $item) => $item->gallery ?? [])->all(),
        );

        return collect($paths)
            ->filter(fn ($path) => is_string($path)
                && preg_match('/\A[a-zA-Z0-9][a-zA-Z0-9_\/.-]*\z/', $path) === 1
                && ! collect(explode('/', $path))->contains(fn ($segment) => in_array($segment, ['', '.', '..'], true))
                && in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif', 'ico'], true))
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @return array<string, array{label: string, url: string}>
     */
    public function options(): array
    {
        $catalogue = app(EncapImageLibrary::class)->catalogue();
        $options = [];

        foreach ($this->paths() as $path) {
            $options[$path] = [
                'label' => basename($path),
                'url' => EncapImage::url($path, $catalogue[$path] ?? null),
            ];
        }

        return $options;
    }
}

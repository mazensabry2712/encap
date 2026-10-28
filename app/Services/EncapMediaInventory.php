<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Company;
use App\Models\SeoSetting;
use App\Support\EncapImage;
use Illuminate\Support\Facades\Storage;
use Throwable;

final class EncapMediaInventory
{
    public function paths(): array
    {
        $catalogue = app(EncapImageLibrary::class)->catalogue();
        $paths = array_merge(
            ['logo.png', 'favicon.png'],
            array_keys($catalogue),
            $this->allFiles('encap_images'),
            $this->allFiles('public'),
            Category::query()->whereNotNull('image')->pluck('image')->all(),
            Company::query()->whereNotNull('image')->pluck('image')->all(),
            SeoSetting::query()->whereNotNull('default_og_image')->pluck('default_og_image')->all(),
            Category::query()->get()->flatMap(fn (Category $item) => $item->gallery ?? [])->all(),
            Company::query()->get()->flatMap(fn (Company $item) => $item->gallery ?? [])->all(),
        );

        return collect($paths)
            ->filter(fn ($path) => is_string($path)
                && preg_match('/\A[a-zA-Z0-9][a-zA-Z0-9_\/.-]*\z/', $path) === 1
                && ! collect(explode('/', $path))->contains(fn ($segment) => in_array($segment, ['', '.', '..'], true))
                && in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif'], true))
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    /**
     * Return disk contents without making the dashboard fail when a storage
     * root has not been created yet or is temporarily inaccessible.
     *
     * @return list<string>
     */
    private function allFiles(string $diskName): array
    {
        try {
            $disk = Storage::disk($diskName);
            $root = $disk->path('');

            if (! is_dir($root)) {
                return [];
            }

            return $disk->allFiles();
        } catch (Throwable) {
            return [];
        }
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

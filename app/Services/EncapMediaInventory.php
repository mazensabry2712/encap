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
            Company::query()->get()->flatMap(fn (Company $item) => collect($item->content_sections ?? [])->flatMap(fn ($section) => $section['images'] ?? [])->all())->all(),
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
     * Every database reference is listed as its own place, even when several
     * records currently point to the same original file.
     *
     * @return list<array{key: string, path: string, location: string}>
     */
    public function usages(): array
    {
        $usages = [];
        $isArabic = app()->getLocale() === 'ar';

        foreach (Category::query()->get(['id', 'name_en', 'name_ar', 'image', 'gallery']) as $category) {
            $name = $isArabic && filled($category->name_ar) ? $category->name_ar : $category->name_en;
            $prefix = $isArabic ? 'المنتج: ' : 'Product: ';

            if (is_string($category->image) && $category->image !== '') {
                $usages[] = [
                    'key' => "category:{$category->id}:image",
                    'path' => $category->image,
                    'location' => $prefix.$name.($isArabic ? ' — الصورة الرئيسية' : ' — main image'),
                ];
            }

            foreach ($category->gallery ?? [] as $index => $path) {
                if (is_string($path) && $path !== '') {
                    $usages[] = [
                        'key' => "category:{$category->id}:gallery:{$index}",
                        'path' => $path,
                        'location' => $prefix.$name.($isArabic ? ' — صورة المعرض '.((int) $index + 1) : ' — gallery image '.((int) $index + 1)),
                    ];
                }
            }
        }

        foreach (Company::query()->get(['id', 'name_en', 'name_ar', 'image', 'gallery', 'content_sections']) as $company) {
            $name = $isArabic && filled($company->name_ar) ? $company->name_ar : $company->name_en;
            $prefix = $isArabic ? 'الشركة: ' : 'Company: ';

            if (is_string($company->image) && $company->image !== '') {
                $usages[] = [
                    'key' => "company:{$company->id}:image",
                    'path' => $company->image,
                    'location' => $prefix.$name.($isArabic ? ' — الصورة الرئيسية' : ' — profile image'),
                ];
            }

            foreach ($company->gallery ?? [] as $index => $path) {
                if (is_string($path) && $path !== '') {
                    $usages[] = [
                        'key' => "company:{$company->id}:gallery:{$index}",
                        'path' => $path,
                        'location' => $prefix.$name.($isArabic ? ' — صورة المعرض '.((int) $index + 1) : ' — gallery image '.((int) $index + 1)),
                    ];
                }
            }

            foreach ($company->content_sections ?? [] as $sectionIndex => $section) {
                $sectionTitle = $isArabic
                    ? ($section['title_ar'] ?? $section['title_en'] ?? '')
                    : ($section['title_en'] ?? $section['title_ar'] ?? '');

                foreach (($section['images'] ?? []) as $imageIndex => $path) {
                    if (is_string($path) && $path !== '') {
                        $sectionName = filled($sectionTitle)
                            ? $sectionTitle
                            : ($isArabic ? 'قسم المحتوى' : 'Content section');

                        $usages[] = [
                            'key' => "company:{$company->id}:section:{$sectionIndex}:image:{$imageIndex}",
                            'path' => $path,
                            'location' => $prefix.$name.' — '.$sectionName.($isArabic ? ' — صورة '.((int) $imageIndex + 1) : ' — image '.((int) $imageIndex + 1)),
                        ];
                    }
                }
            }
        }

        foreach (SeoSetting::query()->whereNotNull('default_og_image')->get(['id', 'default_og_image']) as $settings) {
            if (is_string($settings->default_og_image) && $settings->default_og_image !== '') {
                $usages[] = [
                    'key' => "seo:{$settings->id}:default_og_image",
                    'path' => $settings->default_og_image,
                    'location' => $isArabic ? 'إعدادات SEO — صورة المشاركة الاجتماعية' : 'SEO settings — social sharing image',
                ];
            }
        }

        return $usages;
    }

    public function usage(string $key): ?array
    {
        foreach ($this->usages() as $usage) {
            if ($usage['key'] === $key) {
                return $usage;
            }
        }

        return null;
    }

    /**
     * Change exactly one reference. The expected old path prevents a stale
     * browser tab from overwriting a more recent image selection.
     */
    public function replaceUsage(string $key, string $expectedPath, string $newPath): bool
    {
        $parts = explode(':', $key);

        $replaced = \Illuminate\Support\Facades\DB::transaction(function () use ($parts, $expectedPath, $newPath): bool {
            $type = $parts[0] ?? '';
            $id = isset($parts[1]) && ctype_digit($parts[1]) ? (int) $parts[1] : 0;

            if ($id < 1) {
                return false;
            }

            if ($type === 'category') {
                $category = Category::query()->lockForUpdate()->find($id);

                if (! $category) {
                    return false;
                }

                if (($parts[2] ?? '') === 'image' && count($parts) === 3) {
                    if ($category->image !== $expectedPath) {
                        return false;
                    }

                    $category->image = $newPath;

                    return $category->save();
                }

                if (($parts[2] ?? '') === 'gallery' && count($parts) === 4 && ctype_digit($parts[3])) {
                    $gallery = $category->gallery ?? [];
                    $index = (int) $parts[3];

                    if (! array_key_exists($index, $gallery) || $gallery[$index] !== $expectedPath) {
                        return false;
                    }

                    $gallery[$index] = $newPath;
                    $category->gallery = array_values($gallery);

                    return $category->save();
                }

                return false;
            }

            if ($type === 'company') {
                $company = Company::query()->lockForUpdate()->find($id);

                if (! $company) {
                    return false;
                }

                if (($parts[2] ?? '') === 'image' && count($parts) === 3) {
                    if ($company->image !== $expectedPath) {
                        return false;
                    }

                    $company->image = $newPath;

                    return $company->save();
                }

                if (($parts[2] ?? '') === 'gallery' && count($parts) === 4 && ctype_digit($parts[3])) {
                    $gallery = $company->gallery ?? [];
                    $index = (int) $parts[3];

                    if (! array_key_exists($index, $gallery) || $gallery[$index] !== $expectedPath) {
                        return false;
                    }

                    $gallery[$index] = $newPath;
                    $company->gallery = array_values($gallery);

                    return $company->save();
                }

                if (($parts[2] ?? '') === 'section'
                    && count($parts) === 6
                    && ctype_digit($parts[3])
                    && ($parts[4] ?? '') === 'image'
                    && ctype_digit($parts[5])) {
                    $sections = $company->content_sections ?? [];
                    $sectionIndex = (int) $parts[3];
                    $imageIndex = (int) $parts[5];

                    if (! isset($sections[$sectionIndex]['images'][$imageIndex])
                        || $sections[$sectionIndex]['images'][$imageIndex] !== $expectedPath) {
                        return false;
                    }

                    $sections[$sectionIndex]['images'][$imageIndex] = $newPath;
                    $company->content_sections = $sections;

                    return $company->save();
                }

                return false;
            }

            if ($type === 'seo' && count($parts) === 3 && ($parts[2] ?? '') === 'default_og_image') {
                $settings = SeoSetting::query()->lockForUpdate()->find($id);

                if (! $settings || $settings->default_og_image !== $expectedPath) {
                    return false;
                }

                $settings->default_og_image = $newPath;
                $saved = $settings->save();

                if ($saved) {
                    SeoSetting::clearCache();
                }

                return $saved;
            }

            return false;
        });

        return $replaced;
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

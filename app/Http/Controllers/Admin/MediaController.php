<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company;
use App\Models\SeoSetting;
use App\Services\EncapImageLibrary;
use App\Services\EncapImageUpload;
use App\Services\EncapMediaInventory;
use App\Support\EncapImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class MediaController extends Controller
{
    public function index(Request $request, EncapImageLibrary $imageLibrary, EncapMediaInventory $mediaInventory): View
    {
        $catalogue = $imageLibrary->catalogue();
        $query = trim((string) $request->query('q', ''));

        $staticLabels = [
            'logo.png' => __('Company logo'),
            'favicon.png' => __('Site icon'),
            'orange-orchard-hero-2026.jpg' => __('Homepage hero image'),
            'produce-mix-2026.jpg' => __('Produce mix image'),
            'farm-cultivation-2026.jpg' => __('Agricultural field image'),
            'orchard-harvest-2026.jpg' => __('Orchard harvest image'),
            'citrus-orchard-2026.jpg' => __('Citrus orchard image'),
            'mango-replacement-2026.jpg' => __('Curated mango image'),
            'product-citrus-2026.jpg' => __('Citrus product image'),
            'product-grapes-2026.jpg' => __('Grapes product image'),
            'product-potatoes-2026.jpg' => __('Potato product image'),
            'product-onions-2026.jpg' => __('Onion product image'),
            'product-garlic-2026.jpg' => __('Garlic product image'),
            'product-pomegranates-2026.jpg' => __('Pomegranate product image'),
            'product-peppers-2026.jpg' => __('Pepper product image'),
            'product-strawberries-2026.jpg' => __('Strawberry product image'),
            'potato-sorting-2026.jpg' => __('Potato sorting image'),
            'produce-packing-2026.jpg' => __('Packing station image'),
            'crate-labels-2026.jpg' => __('Produce labels image'),
            'export-crates-2026.jpg' => __('Export crates image'),
            'frozen-berries-2026.jpg' => __('Frozen berries image'),
            'cargo-port-2026.jpg' => __('Export port image'),
        ];

        $allAssets = collect($mediaInventory->paths())->map(function (string $path) use ($catalogue, $staticLabels) {
            return [
                'path' => $path,
                'label' => $staticLabels[$path] ?? basename($path),
                'url' => EncapImage::url($path, $catalogue[$path] ?? null),
            ];
        })->keyBy('path');

        $groupForUsage = function (string $key): string {
            if (str_starts_with($key, 'category:')) {
                return __('Products catalogue');
            }

            if (str_starts_with($key, 'company:')) {
                return __('Company profile');
            }

            if (str_starts_with($key, 'seo:')) {
                return __('SEO & social sharing');
            }

            if (! str_starts_with($key, 'site:')) {
                return __('Website pages');
            }

            $slot = substr($key, 5);
            $parts = explode('.', $slot);
            $page = ($parts[0] ?? '') === 'content'
                ? ($parts[1] ?? '')
                : ($parts[0] ?? '');

            return match ($page) {
                'home' => __('Homepage'),
                'about' => __('About page'),
                'farms' => __('Farms page'),
                'packing-stations' => __('Packing stations'),
                'quality' => __('Quality page'),
                'export' => __('Export page'),
                'products' => __('Products catalogue'),
                'frozen' => __('Frozen catalogue'),
                default => __('Website pages'),
            };
        };

        $usages = $mediaInventory->usages();
        $usedPaths = collect($usages)->pluck('path')->filter()->unique()->values();
        $placements = collect($usages)->map(function (array $usage) use ($allAssets, $groupForUsage, $catalogue) {
            $path = $usage['path'];
            $asset = $allAssets->get($path, []);

            return [
                'path' => $path,
                'label' => $asset['label'] ?? basename($path),
                'url' => $asset['url'] ?? EncapImage::url($path, $catalogue[$path] ?? null),
                'location' => $usage['location'],
                'group' => $groupForUsage($usage['key']),
                'usage_key' => $usage['key'],
                'global_brand_asset' => false,
            ];
        });

        foreach ([
            'logo.png' => ['label' => __('Company logo'), 'location' => __('Main site logo')],
            'favicon.png' => ['label' => __('Site icon'), 'location' => __('Browser and mobile site icon')],
        ] as $path => $brand) {
            $asset = $allAssets->get($path, []);

            $placements->push([
                'path' => $path,
                'label' => $brand['label'],
                'url' => $asset['url'] ?? EncapImage::url($path),
                'location' => $brand['location'],
                'group' => __('Site identity'),
                'usage_key' => null,
                'global_brand_asset' => true,
            ]);
        }

        if ($query !== '') {
            $needle = Str::lower($query);
            $matches = fn (array $item) =>
                Str::contains(Str::lower($item['path']), $needle)
                || Str::contains(Str::lower((string) $item['label']), $needle)
                || Str::contains(Str::lower((string) $item['location']), $needle)
                || Str::contains(Str::lower((string) $item['group']), $needle);

            $placements = $placements->filter($matches)->values();
        }

        $groupOrder = [
            __('Homepage'),
            __('About page'),
            __('Farms page'),
            __('Packing stations'),
            __('Quality page'),
            __('Export page'),
            __('Products catalogue'),
            __('Frozen catalogue'),
            __('Company profile'),
            __('Site identity'),
            __('SEO & social sharing'),
            __('Website pages'),
        ];

        $groups = $placements
            ->groupBy('group')
            ->sortBy(fn ($items, $group) => array_search($group, $groupOrder, true) === false
                ? PHP_INT_MAX
                : array_search($group, $groupOrder, true));

        // Keep unused uploads out of the main location-based library. They remain
        // available in a collapsed section and can only be deleted after a fresh
        // database-reference check by deleteUnused().
        $unusedAssets = $allAssets
            ->reject(fn (array $asset, string $path) => $usedPaths->contains($path)
                || ! preg_match('#\\A(?:site-library|media-replacements|assigned)/#', $path))
            ->values();

        if ($query !== '') {
            $needle = Str::lower($query);
            $unusedAssets = $unusedAssets->filter(fn (array $asset) =>
                Str::contains(Str::lower($asset['path']), $needle)
                || Str::contains(Str::lower($asset['label']), $needle)
            )->values();
        }

        return view('admin.media.index', [
            'groups' => $groups,
            'placementCount' => $placements->count(),
            'totalAssets' => $allAssets->count(),
            'unusedAssets' => $unusedAssets,
            'query' => $query,
        ]);
    }

    public function replace(
        Request $request,
        EncapMediaInventory $mediaInventory,
        EncapImageUpload $imageUpload
    ): RedirectResponse {
        $validated = $request->validate([
            'path' => ['required', 'string', 'max:255'],
            'usage' => ['nullable', 'string', 'max:255'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,avif,gif', 'max:5120'],
        ]);
        $path = ltrim(str_replace('\\\\', '/', $validated['path']), '/');

        if (filled($validated['usage'] ?? null)) {
            $usage = $mediaInventory->usage($validated['usage']);
            abort_unless($usage && $usage['path'] === $path, 404);

            $file = $request->file('image');
            $extension = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension());
            $filename = (string) Str::uuid().'.'.$extension;
            $stored = $imageUpload->storeAs($file, 'media-replacements', $filename, 'image');

            if (! $mediaInventory->replaceUsage($usage['key'], $path, $stored['path'])) {
                Storage::disk($stored['disk'])->delete($stored['path']);
                abort(409, 'This image location has changed. Refresh the media library and try again.');
            }

            $message = app()->getLocale() === 'ar'
                ? 'تم استبدال الصورة في هذا المكان فقط، وباقي الأماكن لم تتغير.'
                : 'Image replaced only in this location. Other places were left unchanged.';

            return back()->with('success', $message)
                ->with('image_storage_fallback', $stored['disk'] === 'public');
        }

        // Brand marks are intentionally shared across the header, footer and metadata.
        // No ordinary content image is allowed to overwrite a shared file path.
        abort_unless(in_array($path, ['logo.png', 'favicon.png'], true), 404);

        $expectedExtension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $uploadedExtension = strtolower($request->file('image')->guessExtension() ?: $request->file('image')->getClientOriginalExtension());
        $extensionAliases = ['jpeg' => 'jpg', 'jpe' => 'jpg'];
        $expectedNormalized = $extensionAliases[$expectedExtension] ?? $expectedExtension;
        $uploadedNormalized = $extensionAliases[$uploadedExtension] ?? $uploadedExtension;

        if ($expectedNormalized !== $uploadedNormalized) {
            throw ValidationException::withMessages([
                'image' => __('The replacement image must use the same file format as the selected image slot.'),
            ]);
        }

        $disk = Storage::disk('encap_images');
        $root = $disk->path('');
        if (! is_dir($root) && ! @mkdir($root, 0755, true) && ! is_dir($root)) {
            throw ValidationException::withMessages(['image' => __('The image could not be saved. Check the image-folder permissions.')]);
        }

        $stream = fopen($request->file('image')->getRealPath(), 'r');
        try {
            $saved = $disk->put($path, $stream);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }
        if (! $saved || ! $disk->exists($path)) {
            throw ValidationException::withMessages(['image' => __('The replacement image could not be confirmed as saved.')]);
        }

        $message = app()->getLocale() === 'ar'
            ? 'تم تحديث ملف هوية الموقع.'
            : 'Brand asset updated.';

        return back()->with('success', $message);
    }

    public function deleteUnused(Request $request, EncapMediaInventory $mediaInventory): RedirectResponse
    {
        $validated = $request->validate([
            'path' => ['required', 'string', 'max:255'],
        ]);

        $path = ltrim(str_replace('\\\\', '/', $validated['path']), '/');
        $segments = explode('/', $path);

        abort_unless(
            in_array($segments[0] ?? '', ['site-library', 'media-replacements', 'assigned'], true)
            && preg_match('/\\A[a-zA-Z0-9][a-zA-Z0-9_\\/.-]*\\z/', $path) === 1
            && ! collect($segments)->contains(fn ($segment) => in_array($segment, ['', '.', '..'], true)),
            404
        );

        $stillUsed = collect($mediaInventory->usages())->contains(
            fn (array $usage) => $usage['path'] === $path
        );

        abort_if($stillUsed, 409, __('This image is still used on the website and cannot be deleted.'));

        $found = false;
        $failed = false;

        foreach (['encap_images', 'public'] as $diskName) {
            try {
                $disk = Storage::disk($diskName);

                if (! $disk->exists($path)) {
                    continue;
                }

                $found = true;

                if (! $disk->delete($path) || $disk->exists($path)) {
                    $failed = true;
                }
            } catch (\\Throwable) {
                $failed = true;
            }
        }

        abort_unless($found, 404);

        if ($failed) {
            return back()->withErrors([
                'image' => __('The unused image could not be fully removed. Check image-folder permissions and try again.'),
            ]);
        }

        return back()->with('success', __('Unused image deleted from the library.'));
    }

    public function upload(Request $request, EncapImageUpload $imageUpload): RedirectResponse
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,avif,gif', 'max:5120'],
            'label' => ['required', 'string', 'max:100'],
        ]);
        $file = $request->file('image');
        $extension = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension());
        $slug = Str::slug($validated['label']) ?: 'site-image';
        $filename = $slug.'-'.Str::lower(Str::random(5)).'.'.$extension;

        // Use the same verified upload/fallback path as product, company and SEO images.
        $stored = $imageUpload->storeAs($file, 'site-library', $filename, 'image');

        return redirect()->route('admin.media.index')
            ->with('success', __('Image added to the central media library.'))
            ->with('image_storage_fallback', $stored['disk'] === 'public');
    }
}

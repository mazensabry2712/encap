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
        $usagesByPath = collect($mediaInventory->usages())->groupBy('path');
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

        $assets = collect($mediaInventory->paths())->map(function (string $path) use ($catalogue, $staticLabels, $usagesByPath) {
            $mainExists = Storage::disk('encap_images')->exists($path);
            $legacyExists = Storage::disk('public')->exists($path);
            $fallback = $catalogue[$path] ?? null;

            return [
                'path' => $path,
                'label' => $staticLabels[$path] ?? basename($path),
                'url' => EncapImage::url($path, $fallback),
                'in_main' => $mainExists,
                'in_legacy' => $legacyExists,
                'usages' => collect($usagesByPath->get($path, []))->values()->all(),
                // The site logo and icon are intentional global brand assets.
                'global_brand_asset' => in_array($path, ['logo.png', 'favicon.png'], true),
                'is_slot' => array_key_exists($path, $staticLabels) || array_key_exists($path, $catalogue),
            ];
        });

        if ($query !== '') {
            $needle = Str::lower($query);
            $assets = $assets->filter(fn (array $asset) =>
                Str::contains(Str::lower($asset['path']), $needle)
                || Str::contains(Str::lower((string) $asset['label']), $needle)
                || collect($asset['usages'])->contains(
                    fn (array $usage) => Str::contains(Str::lower($usage['location']), $needle)
                )
            );
        }

        return view('admin.media.index', [
            'assets' => $assets->values(),
            'totalAssets' => $mediaInventory->paths() ? count($mediaInventory->paths()) : 0,
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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company;
use App\Services\EncapImageLibrary;
use App\Support\EncapImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class MediaController extends Controller
{
    public function index(Request $request, EncapImageLibrary $imageLibrary): View
    {
        $catalogue = $imageLibrary->catalogue();
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

        $paths = collect($paths)
            ->filter(fn ($path) => is_string($path)
                && preg_match('/\A[a-zA-Z0-9][a-zA-Z0-9_\/.-]*\z/', $path) === 1
                && ! collect(explode('/', $path))->contains(fn ($segment) => in_array($segment, ['', '.', '..'], true))
                && in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif', 'ico'], true))
            ->unique()
            ->sort()
            ->values();

        $query = trim((string) $request->query('q', ''));
        if ($query !== '') {
            $paths = $paths->filter(fn (string $path) => Str::contains(Str::lower($path), Str::lower($query)))->values();
        }

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

        $categoryNames = [];
        foreach (Category::query()->get(['name_en', 'name_ar', 'image', 'gallery']) as $category) {
            foreach (array_merge([$category->image], $category->gallery ?? []) as $path) {
                if ($path) {
                    $categoryNames[$path][] = $category->name_en;
                }
            }
        }

        $companyNames = [];
        foreach (Company::query()->get(['name_en', 'image', 'gallery']) as $company) {
            foreach (array_merge([$company->image], $company->gallery ?? []) as $path) {
                if ($path) {
                    $companyNames[$path][] = $company->name_en;
                }
            }
        }

        $assets = $paths->map(function (string $path) use ($catalogue, $staticLabels, $categoryNames, $companyNames) {
            $mainExists = Storage::disk('encap_images')->exists($path);
            $legacyExists = Storage::disk('public')->exists($path);
            $fallback = $catalogue[$path] ?? null;
            $url = EncapImage::url($path, $fallback);

            return [
                'path' => $path,
                'label' => $staticLabels[$path] ?? $path,
                'url' => $url,
                'in_main' => $mainExists,
                'in_legacy' => $legacyExists,
                'categories' => $categoryNames[$path] ?? [],
                'companies' => $companyNames[$path] ?? [],
                'is_slot' => array_key_exists($path, $staticLabels) || array_key_exists($path, $catalogue),
            ];
        });

        return view('admin.media.index', [
            'assets' => $assets,
            'totalAssets' => $paths->count(),
            'query' => $query,
        ]);
    }

    public function replace(Request $request, EncapImageLibrary $imageLibrary): RedirectResponse
    {
        $validated = $request->validate([
            'path' => ['required', 'string', 'max:255'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,avif,gif', 'max:5120'],
        ]);
        $path = ltrim(str_replace('\\', '/', $validated['path']), '/');

        abort_unless(in_array($path, $this->managedPaths($imageLibrary), true), 404);

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
        $directory = dirname($path);
        if ($directory !== '.' && ! is_dir($disk->path($directory))) {
            if (! @mkdir($disk->path($directory), 0755, true) && ! is_dir($disk->path($directory))) {
                throw ValidationException::withMessages(['image' => __('The image could not be saved. Check the image-folder permissions.')]);
            }
        }
        if (! is_writable($directory === '.' ? $disk->path('') : $disk->path($directory))) {
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

        return back()->with('success', __('Image replaced across the website wherever this asset is used.'));
    }

    public function upload(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,avif,gif', 'max:5120'],
            'label' => ['required', 'string', 'max:100'],
        ]);
        $file = $request->file('image');
        $extension = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension());
        $filename = Str::slug($validated['label']).'-'.Str::lower(Str::random(5)).'.'.$extension;
        $stored = $file->storeAs('site-library', $filename, 'encap_images');

        if (! is_string($stored) || ! Storage::disk('encap_images')->exists($stored)) {
            throw ValidationException::withMessages(['image' => __('The image could not be saved. Check the image-folder permissions.')]);
        }

        return redirect()->route('admin.media.index')->with('success', __('Image added to the central media library.'));
    }

    private function managedPaths(EncapImageLibrary $imageLibrary): array
    {
        return array_values(array_unique(array_merge(
            ['logo.png', 'favicon.png'],
            array_keys($imageLibrary->catalogue()),
            Storage::disk('encap_images')->allFiles(),
            Storage::disk('public')->allFiles(),
            Category::query()->whereNotNull('image')->pluck('image')->all(),
            Company::query()->whereNotNull('image')->pluck('image')->all(),
            Category::query()->get()->flatMap(fn (Category $item) => $item->gallery ?? [])->all(),
            Company::query()->get()->flatMap(fn (Company $item) => $item->gallery ?? [])->all(),
        )));
    }
}

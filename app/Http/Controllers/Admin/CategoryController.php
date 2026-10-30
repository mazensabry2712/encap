<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company;
use App\Services\EncapImageAssignment;
use App\Services\EncapImageUpload;
use App\Services\EncapMediaInventory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class CategoryController extends Controller
{
    private const PRODUCT_PLACEMENT_FLAGS = ['show_in_catalogue', 'show_in_frozen_catalogue', 'show_on_homepage'];

    private const PRODUCT_PAGE_FLAGS = [
        'show_product_images', 'show_enquiry_note', 'show_description',
        'show_available_forms', 'show_packaging', 'show_specifications', 'show_bottom_cta',
    ];

    public function index(): View
    {
        return view('admin.categories.index', [
            'categories' => Category::query()->with('companies')->orderBy('sort_order')->orderBy('name_en')->paginate(20),
        ]);
    }

    public function create(EncapMediaInventory $mediaInventory): View
    {
        return view('admin.categories.form', [
            'category' => new Category(),
            'companies' => Company::query()->orderBy('sort_order')->orderBy('name_en')->get(['id', 'name_en', 'name_ar']),
            'mediaAssets' => $mediaInventory->options(),
        ]);
    }

    public function store(Request $request, EncapImageUpload $imageUpload, EncapMediaInventory $mediaInventory, EncapImageAssignment $imageAssignment): RedirectResponse
    {
        $data = $this->formData($request, $mediaInventory);
        $companyIds = array_values(array_unique(array_map('intval', $data['company_ids'] ?? [])));
        unset($data['company_ids']);

        foreach (self::PRODUCT_PLACEMENT_FLAGS as $flag) {
            $data[$flag] = $request->boolean($flag);
        }

        foreach (self::PRODUCT_PAGE_FLAGS as $flag) {
            $data[$flag] = $request->boolean($flag);
        }

        $libraryImage = $data['library_image'] ?? null;
        $libraryGallery = array_values(array_filter($data['library_gallery'] ?? []));
        unset($data['library_image'], $data['library_gallery'], $data['remove_gallery'], $data['images'], $data['gallery']);

        $selectedImages = $this->validImageFiles($request->file('images', []));
        $primaryImage = $selectedImages[0] ?? $this->validImageFile($request->file('image'));
        $storedUploads = [];

        try {
            $data['image'] = null;

            if ($primaryImage) {
                $savedImage = $imageUpload->store($primaryImage, 'categories', 'images');
                $storedUploads[] = $savedImage;
                $data['image'] = $savedImage['path'];
            } elseif ($libraryImage) {
                $data['image'] = $imageAssignment->duplicate($libraryImage, 'category-'.$data['slug'].'-main', 'library_image');
            }

            $galleryPaths = [];
            foreach ($libraryGallery as $index => $libraryPath) {
                $galleryPaths[] = $imageAssignment->duplicate($libraryPath, 'category-'.$data['slug'].'-gallery-'.($index + 1), 'library_gallery.'.$index);
            }
            $galleryUploads = array_merge(
                array_slice($selectedImages, 1),
                $this->validImageFiles($request->file('gallery', []))
            );

            foreach ($galleryUploads as $file) {
                $savedImage = $imageUpload->store($file, 'categories/gallery', 'gallery');
                $storedUploads[] = $savedImage;
                $galleryPaths[] = $savedImage['path'];
            }

            $data['gallery'] = $galleryPaths;
            $data['is_published'] = $request->boolean('is_published');
            $data['available_forms'] = array_values(array_intersect(
                $data['available_forms'] ?? [],
                ['Fresh', 'Frozen', 'Processed']
            ));
            if ($data['show_in_frozen_catalogue']) {
                $data['available_forms'] = array_values(array_unique([...$data['available_forms'], 'Frozen']));
            }

            $category = Category::create($data);
            $category->companies()->sync($companyIds);
        } catch (Throwable $exception) {
            $this->removeStoredUploads($storedUploads);
            throw $exception;
        }

        $usedFallbackStorage = collect($storedUploads)->contains(
            fn (array $upload): bool => $upload['disk'] === 'public'
        );

        return redirect()->route('admin.categories.index')
            ->with('success', __('Category created.'))
            ->with('image_storage_fallback', $usedFallbackStorage);
    }

    public function edit(Category $category, EncapMediaInventory $mediaInventory): View
    {
        return view('admin.categories.form', [
            'category' => $category,
            'companies' => Company::query()->orderBy('sort_order')->orderBy('name_en')->get(['id', 'name_en', 'name_ar']),
            'mediaAssets' => $mediaInventory->options(),
        ]);
    }

    public function update(Request $request, Category $category, EncapImageUpload $imageUpload, EncapMediaInventory $mediaInventory, EncapImageAssignment $imageAssignment): RedirectResponse
    {
        $data = $this->formData($request, $mediaInventory, $category);
        $companyIds = array_values(array_unique(array_map('intval', $data['company_ids'] ?? [])));
        unset($data['company_ids']);

        foreach (self::PRODUCT_PLACEMENT_FLAGS as $flag) {
            $data[$flag] = $request->boolean($flag);
        }

        foreach (self::PRODUCT_PAGE_FLAGS as $flag) {
            $data[$flag] = $request->boolean($flag);
        }

        $libraryImage = $data['library_image'] ?? null;
        $libraryGallery = array_values(array_filter($data['library_gallery'] ?? []));
        $removeGallery = $data['remove_gallery'] ?? [];
        unset($data['library_image'], $data['library_gallery'], $data['remove_gallery'], $data['images'], $data['gallery'], $data['image']);

        $selectedImages = $this->validImageFiles($request->file('images', []));
        $primaryImage = $selectedImages[0] ?? $this->validImageFile($request->file('image'));
        $storedUploads = [];

        try {
            if ($primaryImage) {
                $savedImage = $imageUpload->store($primaryImage, 'categories', 'images');
                $storedUploads[] = $savedImage;
                $data['image'] = $savedImage['path'];
            } elseif ($libraryImage) {
                $data['image'] = $imageAssignment->duplicate($libraryImage, 'category-'.$category->slug.'-main', 'library_image');
            }

            $gallery = $category->gallery ?? [];

            foreach ($removeGallery as $remove) {
                if (array_key_exists((int) $remove, $gallery)) {
                    unset($gallery[(int) $remove]);
                }
            }

            $gallery = array_values($gallery);
            foreach ($libraryGallery as $index => $libraryPath) {
                $gallery[] = $imageAssignment->duplicate($libraryPath, 'category-'.$category->slug.'-gallery-'.($index + 1), 'library_gallery.'.$index);
            }
            $galleryUploads = array_merge(
                array_slice($selectedImages, 1),
                $this->validImageFiles($request->file('gallery', []))
            );

            foreach ($galleryUploads as $file) {
                $savedImage = $imageUpload->store($file, 'categories/gallery', 'gallery');
                $storedUploads[] = $savedImage;
                $gallery[] = $savedImage['path'];
            }

            $data['gallery'] = array_values(array_unique($gallery));
            $data['is_published'] = $request->boolean('is_published');
            $data['available_forms'] = array_values(array_intersect(
                $data['available_forms'] ?? [],
                ['Fresh', 'Frozen', 'Processed']
            ));
            if ($data['show_in_frozen_catalogue']) {
                $data['available_forms'] = array_values(array_unique([...$data['available_forms'], 'Frozen']));
            }

            $category->update($data);
            $category->companies()->sync($companyIds);
        } catch (Throwable $exception) {
            $this->removeStoredUploads($storedUploads);
            throw $exception;
        }

        // Keep old media files available in the central library. Other pages may
        // reference them even after this category stops using them.

        $usedFallbackStorage = collect($storedUploads)->contains(
            fn (array $upload): bool => $upload['disk'] === 'public'
        );

        return redirect()->route('admin.categories.index')
            ->with('success', __('Category updated.'))
            ->with('image_storage_fallback', $usedFallbackStorage);
    }

    public function destroy(Category $category): RedirectResponse
    {
        // Media stays available in the central library even if a category is deleted.
        $category->delete();

        return back()->with('success', __('Category deleted.'));
    }

    /**
     * @param array<array{path: string, disk: string}> $uploads
     */
    private function removeStoredUploads(array $uploads): void
    {
        foreach ($uploads as $upload) {
            Storage::disk($upload['disk'])->delete($upload['path']);
        }
    }

    /**
     * Collects product form data without blocking saves for optional fields.
     * Database-required fields receive safe automatic defaults; invalid optional
     * relationships and media selections are simply ignored.
     */
    private function formData(Request $request, EncapMediaInventory $mediaInventory, ?Category $category = null): array
    {
        $limits = [
            'name_en' => 255,
            'name_ar' => 255,
            'short_description_en' => 500,
            'short_description_ar' => 500,
            'description_en' => null,
            'description_ar' => null,
            'varieties_en' => null,
            'varieties_ar' => null,
            'harvest_window_en' => 255,
            'harvest_window_ar' => 255,
            'specifications_en' => null,
            'specifications_ar' => null,
            'packaging_en' => 255,
            'packaging_ar' => 255,
            'overview_heading_en' => 255,
            'overview_heading_ar' => 255,
            'enquiry_note_heading_en' => 160,
            'enquiry_note_heading_ar' => 160,
            'enquiry_note_text_en' => null,
            'enquiry_note_text_ar' => null,
            'bottom_cta_heading_en' => 255,
            'bottom_cta_heading_ar' => 255,
            'bottom_cta_text_en' => null,
            'bottom_cta_text_ar' => null,
            'seo_title_en' => 255,
            'seo_title_ar' => 255,
            'seo_description_en' => 500,
            'seo_description_ar' => 500,
        ];

        $data = [];

        foreach ($limits as $field => $limit) {
            $value = $request->exists($field)
                ? $request->input($field)
                : $category?->{$field};

            $value = is_scalar($value) ? trim((string) $value) : null;

            if ($value === '') {
                $value = null;
            }

            if ($value !== null && $limit !== null) {
                $value = mb_substr($value, 0, $limit);
            }

            $data[$field] = $value;
        }

        // Keep the non-null database columns populated without forcing the user
        // to enter an English name, slug, or display order.
        $data['name_en'] = $data['name_en']
            ?: ($data['name_ar'] ?: ($category?->name_en ?: 'Product'));

        $requestedSlug = $request->exists('slug')
            ? $request->input('slug')
            : $category?->slug;
        $requestedSlug = is_scalar($requestedSlug) ? trim((string) $requestedSlug) : '';
        $slugSource = $requestedSlug !== '' ? $requestedSlug : $data['name_en'];
        $baseSlug = Str::slug($slugSource);
        $baseSlug = mb_substr($baseSlug !== '' ? $baseSlug : 'product', 0, 220);
        $data['slug'] = $this->uniqueSlug($baseSlug, $category);

        $requestedOrder = $request->exists('sort_order')
            ? $request->input('sort_order')
            : $category?->sort_order;
        $data['sort_order'] = is_numeric($requestedOrder)
            ? min(4294967295, max(0, (int) $requestedOrder))
            : (int) ($category?->sort_order ?? 0);

        $rawCompanyIds = ($request->exists('company_ids_present') || $request->exists('company_ids'))
            ? (array) $request->input('company_ids', [])
            : ($category?->companies()->pluck('companies.id')->all() ?? []);
        $companyIds = collect($rawCompanyIds)
            ->filter(fn ($id) => is_scalar($id) && ctype_digit((string) $id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();
        $data['company_ids'] = Company::query()
            ->whereIn('id', $companyIds)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $rawForms = ($request->exists('available_forms_present') || $request->exists('available_forms'))
            ? (array) $request->input('available_forms', [])
            : ($category?->available_forms ?? []);
        $rawForms = array_values(array_filter($rawForms, fn ($form) => is_string($form)));
        $data['available_forms'] = array_values(array_intersect(
            $rawForms,
            ['Fresh', 'Frozen', 'Processed']
        ));

        $allowedLibraryPaths = $mediaInventory->paths();
        $libraryImage = $request->input('library_image');
        $data['library_image'] = is_string($libraryImage)
            && in_array($libraryImage, $allowedLibraryPaths, true)
                ? $libraryImage
                : null;

        $data['library_gallery'] = array_values(array_unique(array_filter(
            (array) $request->input('library_gallery', []),
            fn ($path) => is_string($path) && in_array($path, $allowedLibraryPaths, true)
        )));

        $currentGallery = $category?->gallery ?? [];
        $data['remove_gallery'] = array_values(array_unique(array_filter(
            (array) $request->input('remove_gallery', []),
            fn ($index) => is_scalar($index)
                && filter_var($index, FILTER_VALIDATE_INT) !== false
                && array_key_exists((int) $index, $currentGallery)
        )));

        return $data;
    }

    private function uniqueSlug(string $baseSlug, ?Category $category = null): string
    {
        $slug = $baseSlug;
        $suffix = 2;

        while (Category::query()
            ->where('slug', $slug)
            ->when($category, fn ($query) => $query->where('id', '<>', $category->getKey()))
            ->exists()) {
            $suffixText = '-'.$suffix++;
            $slug = mb_substr($baseSlug, 0, 220 - mb_strlen($suffixText)).$suffixText;
        }

        return $slug;
    }

    /**
     * Only real supported image uploads are stored. A missing, oversized, or
     * unsupported image does not prevent the product's text fields from saving.
     */
    private function validImageFile(mixed $file): ?UploadedFile
    {
        if (! $file instanceof UploadedFile || ! $file->isValid()) {
            return null;
        }

        $mimeType = strtolower((string) $file->getMimeType());

        // Allow normal raster formats (including newer formats and GIF) without
        // constraining the upload to a short extension list. SVG stays excluded
        // because active SVG content is unsafe to serve as a public image.
        return str_starts_with($mimeType, 'image/')
            && $mimeType !== 'image/svg+xml'
                ? $file
                : null;
    }

    /**
     * @return array<int, UploadedFile>
     */
    private function validImageFiles(mixed $files): array
    {
        return collect(is_array($files) ? $files : [])
            ->map(fn ($file) => $this->validImageFile($file))
            ->filter()
            ->values()
            ->all();
    }

}

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
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
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
        $data = $this->validated($request, $mediaInventory);
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

        $selectedImages = array_values(array_filter((array) $request->file('images', [])));
        $primaryImage = $selectedImages[0] ?? $request->file('image');
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
                array_values(array_filter((array) $request->file('gallery', [])))
            );

            foreach ($galleryUploads as $file) {
                $savedImage = $imageUpload->store($file, 'categories/gallery', 'gallery');
                $storedUploads[] = $savedImage;
                $galleryPaths[] = $savedImage['path'];
            }

            $data['gallery'] = $galleryPaths;
            $data['is_published'] = $request->boolean('is_published');
            $data['available_forms'] = array_values(array_intersect(
                $request->input('available_forms', []),
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
        $data = $this->validated($request, $mediaInventory, $category);
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
        unset($data['library_image'], $data['library_gallery'], $data['remove_gallery'], $data['images'], $data['gallery'], $data['image']);

        $selectedImages = array_values(array_filter((array) $request->file('images', [])));
        $primaryImage = $selectedImages[0] ?? $request->file('image');
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

            foreach ($request->input('remove_gallery', []) as $remove) {
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
                array_values(array_filter((array) $request->file('gallery', [])))
            );

            foreach ($galleryUploads as $file) {
                $savedImage = $imageUpload->store($file, 'categories/gallery', 'gallery');
                $storedUploads[] = $savedImage;
                $gallery[] = $savedImage['path'];
            }

            $data['gallery'] = array_values(array_unique($gallery));
            $data['is_published'] = $request->boolean('is_published');
            $data['available_forms'] = array_values(array_intersect(
                $request->input('available_forms', []),
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

    private function validated(Request $request, EncapMediaInventory $mediaInventory, ?Category $category = null): array
    {
        $currentGallery = $category?->gallery ?? [];
        $requestedRemovals = collect($request->input('remove_gallery', []))
            ->filter(fn ($index) => is_scalar($index)
                && filter_var($index, FILTER_VALIDATE_INT) !== false
                && array_key_exists((int) $index, $currentGallery))
            ->map(fn ($index) => (int) $index)
            ->unique()
            ->count();
        $remainingGalleryCount = max(0, count($currentGallery) - $requestedRemovals);
        $selectedImages = array_values(array_filter((array) $request->file('images', [])));
        $libraryGalleryCount = count(array_filter((array) $request->input('library_gallery', [])));
        $uploadedSelectionGalleryCount = max(0, count($selectedImages) - 1);
        $libraryGallerySlots = max(0, 12 - $remainingGalleryCount - $uploadedSelectionGalleryCount);
        $batchGalleryCount = $uploadedSelectionGalleryCount + $libraryGalleryCount;
        $gallerySlotsAvailable = max(0, 12 - $remainingGalleryCount - $batchGalleryCount);
        $productImageBatchLimit = 1 + max(0, 12 - $remainingGalleryCount - $libraryGalleryCount);

        return $request->validate([
            'name_en' => ['required', 'string', 'max:160'],
            'name_ar' => ['nullable', 'string', 'max:160'],
            'slug' => ['required', 'alpha_dash', 'max:180', Rule::unique('categories', 'slug')->ignore($category?->id)],
            'short_description_en' => ['nullable', 'string', 'max:500'],
            'short_description_ar' => ['nullable', 'string', 'max:500'],
            'description_en' => ['nullable', 'string', 'max:20000'],
            'description_ar' => ['nullable', 'string', 'max:20000'],
            'varieties_en' => ['nullable', 'string', 'max:2000'],
            'varieties_ar' => ['nullable', 'string', 'max:2000'],
            'harvest_window_en' => ['nullable', 'string', 'max:255'],
            'harvest_window_ar' => ['nullable', 'string', 'max:255'],
            'specifications_en' => ['nullable', 'string', 'max:2000'],
            'specifications_ar' => ['nullable', 'string', 'max:2000'],
            'packaging_en' => ['nullable', 'string', 'max:255'],
            'packaging_ar' => ['nullable', 'string', 'max:255'],
            'overview_heading_en' => ['nullable', 'string', 'max:255'],
            'overview_heading_ar' => ['nullable', 'string', 'max:255'],
            'enquiry_note_heading_en' => ['nullable', 'string', 'max:160'],
            'enquiry_note_heading_ar' => ['nullable', 'string', 'max:160'],
            'enquiry_note_text_en' => ['nullable', 'string', 'max:1000'],
            'enquiry_note_text_ar' => ['nullable', 'string', 'max:1000'],
            'bottom_cta_heading_en' => ['nullable', 'string', 'max:255'],
            'bottom_cta_heading_ar' => ['nullable', 'string', 'max:255'],
            'bottom_cta_text_en' => ['nullable', 'string', 'max:2000'],
            'bottom_cta_text_ar' => ['nullable', 'string', 'max:2000'],
            'show_in_catalogue' => ['sometimes', 'boolean'],
            'show_in_frozen_catalogue' => ['sometimes', 'boolean'],
            'show_on_homepage' => ['sometimes', 'boolean'],
            'company_ids' => ['nullable', 'array'],
            'company_ids.*' => ['integer', 'exists:companies,id'],
            'show_product_images' => ['sometimes', 'boolean'],
            'show_enquiry_note' => ['sometimes', 'boolean'],
            'show_description' => ['sometimes', 'boolean'],
            'show_available_forms' => ['sometimes', 'boolean'],
            'show_packaging' => ['sometimes', 'boolean'],
            'show_specifications' => ['sometimes', 'boolean'],
            'show_bottom_cta' => ['sometimes', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'seo_title_en' => ['nullable', 'string', 'max:255'],
            'seo_title_ar' => ['nullable', 'string', 'max:255'],
            'seo_description_en' => ['nullable', 'string', 'max:500'],
            'seo_description_ar' => ['nullable', 'string', 'max:500'],
            'images' => ['nullable', 'array', 'max:' . $productImageBatchLimit],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
            'library_image' => ['nullable', 'string', Rule::in($mediaInventory->paths())],
            'library_gallery' => ['nullable', 'array', 'max:' . $libraryGallerySlots],
            'library_gallery.*' => ['string', Rule::in($mediaInventory->paths())],
            'gallery' => ['nullable', 'array', 'max:' . $gallerySlotsAvailable],
            'gallery.*' => ['image', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
            'remove_gallery' => ['nullable', 'array'],
            'remove_gallery.*' => ['integer', 'min:0'],
            'available_forms' => ['nullable', 'array'],
            'available_forms.*' => ['in:Fresh,Frozen,Processed'],
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Company;
use App\Services\EncapImageUpload;
use App\Services\EncapMediaInventory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class CompanyController extends Controller
{
    private const COMPANY_SLUG = 'hadaeq-al-shamal';

    public function index(): View
    {
        $company = Company::query()->where('slug', self::COMPANY_SLUG)->firstOrFail();

        return view('admin.companies.index', compact('company'));
    }

    public function edit(Company $company, EncapMediaInventory $mediaInventory): View
    {
        $this->ensureFixedCompany($company);

        return view('admin.companies.form', [
            'company' => $company,
            'categories' => Category::query()
                ->orderBy('sort_order')
                ->orderBy('name_en')
                ->get(['id', 'name_en', 'name_ar', 'is_published']),
            'mediaAssets' => $mediaInventory->options(),
        ]);
    }

    public function update(
        Request $request,
        Company $company,
        EncapImageUpload $imageUpload,
        EncapMediaInventory $mediaInventory
    ): RedirectResponse {
        $this->ensureFixedCompany($company);

        $data = $this->validated($request, $mediaInventory, $company);
        $categoryIds = array_values(array_unique(array_map('intval', $data['category_ids'] ?? [])));
        $sectionInput = $data['sections'] ?? [];
        unset($data['category_ids'], $data['sections']);

        $libraryImage = $data['library_image'] ?? null;
        $libraryGallery = array_values(array_filter($data['library_gallery'] ?? []));
        unset($data['library_image'], $data['library_gallery'], $data['gallery'], $data['remove_gallery']);

        if ($request->hasFile('image')) {
            $stored = $imageUpload->store($request->file('image'), 'companies', 'image');
            $data['image'] = $stored['path'];
        } elseif ($libraryImage) {
            $data['image'] = $libraryImage;
        } else {
            unset($data['image']);
        }

        $gallery = $company->gallery ?? [];
        foreach ($request->input('remove_gallery', []) as $remove) {
            if (array_key_exists((int) $remove, $gallery)) {
                unset($gallery[(int) $remove]);
            }
        }
        $gallery = array_values(array_unique(array_merge(array_values($gallery), $libraryGallery)));

        foreach (array_values(array_filter((array) $request->file('gallery', []))) as $file) {
            $stored = $imageUpload->store($file, 'companies/gallery', 'gallery');
            $gallery[] = $stored['path'];
        }

        $data['gallery'] = array_values(array_unique($gallery));
        $data['content_sections'] = $this->buildContentSections($request, $imageUpload, $sectionInput);
        $data['is_published'] = true;

        $company->update($data);
        $company->categories()->sync($categoryIds);

        return redirect()->route('admin.companies.edit', $company)
            ->with('success', __('Company updated.'));
    }

    private function ensureFixedCompany(Company $company): void
    {
        abort_unless($company->slug === self::COMPANY_SLUG, 404);
    }

    /**
     * Resolve the images for each administrator-defined content section.
     * Existing and library images are reused; uploaded images are stored separately.
     *
     * @param array<int|string, array<string, mixed>> $input
     * @return list<array{title_ar: string, title_en: string, images: list<string>}>
     */
    private function buildContentSections(
        Request $request,
        EncapImageUpload $imageUpload,
        array $input
    ): array {
        $prepared = [];

        foreach ($input as $index => $section) {
            if (! is_array($section)) {
                continue;
            }

            $titleAr = trim((string) ($section['title_ar'] ?? ''));
            $titleEn = trim((string) ($section['title_en'] ?? ''));
            $existingImages = (array) ($section['existing_images'] ?? []);
            $libraryImages = (array) ($section['library_images'] ?? []);
            $sources = [];
            $hasAnyContent = $titleAr !== '' || $titleEn !== '';

            for ($position = 0; $position < 2; $position++) {
                $upload = $request->file("sections.{$index}.upload_images.{$position}");
                $libraryPath = $libraryImages[$position] ?? null;
                $existingPath = $existingImages[$position] ?? null;

                $sources[$position] = [
                    'upload' => $upload,
                    'library' => is_string($libraryPath) ? $libraryPath : null,
                    'existing' => is_string($existingPath) ? $existingPath : null,
                ];

                if ($upload || filled($libraryPath) || filled($existingPath)) {
                    $hasAnyContent = true;
                }
            }

            // Ignore a completely blank, unused repeater row.
            if (! $hasAnyContent) {
                continue;
            }

            if ($titleAr === '' && $titleEn === '') {
                throw ValidationException::withMessages([
                    "sections.{$index}.title_ar" => __('Add an Arabic or English title for each section.'),
                ]);
            }

            foreach ($sources as $position => $source) {
                if (! $source['upload'] && ! filled($source['library']) && ! filled($source['existing'])) {
                    throw ValidationException::withMessages([
                        "sections.{$index}.upload_images.{$position}" => __('Choose or upload both images for each section.'),
                    ]);
                }
            }

            $prepared[] = [
                'index' => $index,
                'title_ar' => $titleAr,
                'title_en' => $titleEn,
                'sources' => $sources,
            ];
        }

        if ($prepared === []) {
            throw ValidationException::withMessages([
                'sections' => __('Keep at least one company content section.'),
            ]);
        }

        $sections = [];

        foreach ($prepared as $section) {
            $imagePaths = [];

            foreach ($section['sources'] as $position => $source) {
                if ($source['upload']) {
                    $stored = $imageUpload->store(
                        $source['upload'],
                        'companies/sections',
                        "sections.{$section['index']}.upload_images.{$position}"
                    );
                    $imagePaths[] = $stored['path'];
                } elseif (filled($source['library'])) {
                    $imagePaths[] = $source['library'];
                } else {
                    $imagePaths[] = $source['existing'];
                }
            }

            $titleAr = $section['title_ar'] ?: $section['title_en'];
            $titleEn = $section['title_en'] ?: $section['title_ar'];

            $sections[] = [
                'title_ar' => $titleAr,
                'title_en' => $titleEn,
                'images' => $imagePaths,
            ];
        }

        return $sections;
    }

    private function validated(Request $request, EncapMediaInventory $mediaInventory, Company $company): array
    {
        $currentGallery = $company->gallery ?? [];
        $requestedRemovals = collect($request->input('remove_gallery', []))
            ->filter(fn ($index) => is_scalar($index)
                && filter_var($index, FILTER_VALIDATE_INT) !== false
                && array_key_exists((int) $index, $currentGallery))
            ->map(fn ($index) => (int) $index)
            ->unique()
            ->count();
        $remainingGalleryCount = max(0, count($currentGallery) - $requestedRemovals);
        $libraryGalleryCount = count(array_filter((array) $request->input('library_gallery', [])));
        $uploadedGalleryCount = count(array_filter((array) $request->file('gallery', [])));
        $libraryGallerySlots = max(0, 12 - $remainingGalleryCount - $uploadedGalleryCount);
        $uploadedGallerySlots = max(0, 12 - $remainingGalleryCount - $libraryGalleryCount);

        return $request->validate([
            'short_description_en' => ['nullable', 'string', 'max:500'],
            'short_description_ar' => ['nullable', 'string', 'max:500'],
            'description_en' => ['nullable', 'string', 'max:20000'],
            'description_ar' => ['nullable', 'string', 'max:20000'],
            'country_en' => ['nullable', 'string', 'max:120'],
            'country_ar' => ['nullable', 'string', 'max:120'],
            'location_en' => ['nullable', 'string', 'max:160'],
            'location_ar' => ['nullable', 'string', 'max:160'],
            'website_url' => ['nullable', 'url', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
            'library_image' => ['nullable', 'string', Rule::in($mediaInventory->paths())],
            'gallery' => ['nullable', 'array', 'max:' . $uploadedGallerySlots],
            'gallery.*' => ['image', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
            'library_gallery' => ['nullable', 'array', 'max:' . $libraryGallerySlots],
            'library_gallery.*' => ['string', Rule::in($mediaInventory->paths())],
            'remove_gallery' => ['nullable', 'array'],
            'remove_gallery.*' => ['integer', 'min:0'],
            'sections' => ['nullable', 'array', 'max:12'],
            'sections.*.title_ar' => ['nullable', 'string', 'max:200'],
            'sections.*.title_en' => ['nullable', 'string', 'max:200'],
            'sections.*.existing_images' => ['nullable', 'array', 'size:2'],
            'sections.*.existing_images.*' => ['nullable', 'string', Rule::in($mediaInventory->paths())],
            'sections.*.library_images' => ['nullable', 'array', 'size:2'],
            'sections.*.library_images.*' => ['nullable', 'string', Rule::in($mediaInventory->paths())],
            'sections.*.upload_images' => ['nullable', 'array', 'size:2'],
            'sections.*.upload_images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'distinct', 'exists:categories,id'],
        ]);
    }
}

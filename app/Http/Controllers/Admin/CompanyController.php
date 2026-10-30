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
use Illuminate\Support\Str;
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
        EncapMediaInventory $mediaInventory,
        EncapImageAssignment $imageAssignment
    ): RedirectResponse {
        $this->ensureFixedCompany($company);

        $data = $this->formData($request, $mediaInventory, $company);
        $hasCategorySelection = array_key_exists('category_ids', $data);
        $categoryIds = $data['category_ids'] ?? [];
        $hasSections = array_key_exists('sections', $data);
        $sectionInput = $data['sections'] ?? [];
        unset($data['category_ids'], $data['sections']);

        $libraryImage = $data['library_image'] ?? null;
        $libraryGallery = $data['library_gallery'] ?? [];
        $removeGallery = $data['remove_gallery'] ?? [];
        unset($data['library_image'], $data['library_gallery'], $data['remove_gallery']);

        $mainImage = $this->validImageFile($request->file('image'));
        if ($mainImage) {
            $stored = $imageUpload->store($mainImage, 'companies', 'image');
            $data['image'] = $stored['path'];
        } elseif ($libraryImage) {
            $data['image'] = $imageAssignment->duplicate(
                $libraryImage,
                'company-'.$company->slug.'-profile',
                'library_image'
            );
        }

        $gallery = $company->gallery ?? [];
        foreach ($removeGallery as $remove) {
            if (array_key_exists((int) $remove, $gallery)) {
                unset($gallery[(int) $remove]);
            }
        }
        $gallery = array_values($gallery);

        foreach ($libraryGallery as $index => $libraryPath) {
            $gallery[] = $imageAssignment->duplicate(
                $libraryPath,
                'company-'.$company->slug.'-gallery-'.($index + 1),
                'library_gallery.'.$index
            );
        }

        foreach ($this->validImageFiles($request->file('gallery', [])) as $file) {
            $stored = $imageUpload->store($file, 'companies/gallery', 'gallery');
            $gallery[] = $stored['path'];
        }

        $data['gallery'] = array_values(array_unique($gallery));

        // A partial profile update leaves all existing sections untouched.
        // An explicit empty sections_present marker lets the admin remove them all.
        if ($hasSections) {
            $data['content_sections'] = $this->buildContentSections(
                $request,
                $imageUpload,
                $imageAssignment,
                $mediaInventory,
                is_array($sectionInput) ? $sectionInput : [],
                $company
            );
        }

        $data['is_published'] = true;

        $company->update($data);

        if ($hasCategorySelection) {
            $company->categories()->sync($categoryIds);
        }

        return redirect()->route('admin.companies.edit', $company)
            ->with('success', __('Company updated.'));
    }

    private function ensureFixedCompany(Company $company): void
    {
        abort_unless($company->slug === self::COMPANY_SLUG, 404);
    }

    /**
     * Rebuild only submitted sections. Every section may contain any number of
     * images; empty titles and image slots are allowed.
     *
     * @param array<int|string, array<string, mixed>> $input
     * @return list<array{title_ar: string, title_en: string, images: list<string>}>
     */
    private function buildContentSections(
        Request $request,
        EncapImageUpload $imageUpload,
        EncapImageAssignment $imageAssignment,
        EncapMediaInventory $mediaInventory,
        array $input,
        Company $company
    ): array {
        $allowedLibraryPaths = $mediaInventory->paths();
        $existingCompanyPaths = collect(is_array($company->content_sections) ? $company->content_sections : Company::defaultContentSections())
            ->flatMap(fn (array $section) => $section['images'] ?? [])
            ->filter(fn ($path) => is_string($path) && $path !== '')
            ->values()
            ->all();

        $sections = [];

        foreach ($input as $sectionIndex => $section) {
            if (! is_array($section)) {
                continue;
            }

            $titleAr = $this->plainText($section['title_ar'] ?? '', 200);
            $titleEn = $this->plainText($section['title_en'] ?? '', 200);
            $existingImages = is_array($section['existing_images'] ?? null) ? $section['existing_images'] : [];
            $libraryImages = is_array($section['library_images'] ?? null) ? $section['library_images'] : [];
            $uploadedImages = $request->file("sections.{$sectionIndex}.upload_images", []);
            $uploadedImages = is_array($uploadedImages) ? $uploadedImages : [];

            $positions = array_unique(array_merge(
                array_keys($existingImages),
                array_keys($libraryImages),
                array_keys($uploadedImages)
            ));
            usort($positions, fn ($left, $right) => (int) $left <=> (int) $right);

            $imagePaths = [];

            foreach ($positions as $imagePosition) {
                $upload = $this->validImageFile($uploadedImages[$imagePosition] ?? null);
                $libraryPath = $libraryImages[$imagePosition] ?? null;
                $existingPath = $existingImages[$imagePosition] ?? null;

                if ($upload) {
                    $stored = $imageUpload->store(
                        $upload,
                        'companies/sections',
                        "sections.{$sectionIndex}.upload_images.{$imagePosition}"
                    );
                    $imagePaths[] = $stored['path'];
                } elseif (is_string($libraryPath) && in_array($libraryPath, $allowedLibraryPaths, true)) {
                    $imagePaths[] = $imageAssignment->duplicate(
                        $libraryPath,
                        'company-'.$company->slug.'-section-'.$sectionIndex.'-image-'.$imagePosition,
                        "sections.{$sectionIndex}.library_images.{$imagePosition}"
                    );
                } elseif (is_string($existingPath) && in_array($existingPath, $existingCompanyPaths, true)) {
                    $imagePaths[] = $existingPath;
                }
            }

            // Completely blank repeater rows are ignored; partially completed
            // sections are kept without demanding titles or a fixed image count.
            if ($titleAr === '' && $titleEn === '' && $imagePaths === []) {
                continue;
            }

            $titleAr = $titleAr ?: ($titleEn ?: 'معرض الصور');
            $titleEn = $titleEn ?: ($titleAr ?: 'Photo gallery');

            $sections[] = [
                'title_ar' => $titleAr,
                'title_en' => $titleEn,
                'images' => array_values($imagePaths),
            ];
        }

        return $sections;
    }

    /**
     * Normalize only submitted fields. Missing fields are deliberately omitted
     * so one-field edits never clear the rest of the company profile.
     */
    private function formData(Request $request, EncapMediaInventory $mediaInventory, Company $company): array
    {
        $limits = [
            'short_description_en' => 500,
            'short_description_ar' => 500,
            'description_en' => null,
            'description_ar' => null,
            'country_en' => 120,
            'country_ar' => 120,
            'location_en' => 160,
            'location_ar' => 160,
        ];

        $data = [];

        foreach ($limits as $field => $limit) {
            if (! $request->exists($field)) {
                continue;
            }

            $value = $this->plainText($request->input($field), $limit);
            $data[$field] = $value === '' ? null : $value;
        }

        if ($request->exists('website_url')) {
            $url = trim((string) $request->input('website_url', ''));

            if ($url === '') {
                $data['website_url'] = null;
            } else {
                if (! preg_match('/\Ahttps?:\/\//i', $url)) {
                    $url = 'https://'.$url;
                }

                $parts = parse_url($url);
                $isSafeUrl = is_array($parts)
                    && in_array(strtolower((string) ($parts['scheme'] ?? '')), ['http', 'https'], true)
                    && filter_var($url, FILTER_VALIDATE_URL) !== false
                    && filled($parts['host'] ?? null);

                // Ignore malformed links rather than rejecting the whole form.
                if ($isSafeUrl) {
                    $data['website_url'] = mb_substr($url, 0, 500);
                }
            }
        }

        if ($request->exists('category_ids_present') || $request->exists('category_ids')) {
            $rawIds = (array) $request->input('category_ids', []);
            $ids = collect($rawIds)
                ->filter(fn ($id) => is_scalar($id) && ctype_digit((string) $id))
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            $data['category_ids'] = Category::query()
                ->whereIn('id', $ids)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        if ($request->exists('sections_present') || $request->exists('sections')) {
            $sections = $request->input('sections', []);
            $data['sections'] = is_array($sections) ? $sections : [];
        }

        $allowedPaths = $mediaInventory->paths();

        if ($request->exists('library_image')) {
            $path = $request->input('library_image');
            if (is_string($path) && in_array($path, $allowedPaths, true)) {
                $data['library_image'] = $path;
            }
        }

        if ($request->exists('library_gallery')) {
            $data['library_gallery'] = array_values(array_unique(array_filter(
                (array) $request->input('library_gallery', []),
                fn ($path) => is_string($path) && in_array($path, $allowedPaths, true)
            )));
        }

        if ($request->exists('remove_gallery')) {
            $currentGallery = $company->gallery ?? [];
            $data['remove_gallery'] = array_values(array_unique(array_filter(
                (array) $request->input('remove_gallery', []),
                fn ($index) => is_scalar($index)
                    && filter_var($index, FILTER_VALIDATE_INT) !== false
                    && array_key_exists((int) $index, $currentGallery)
            )));
        }

        return $data;
    }

    private function plainText(mixed $value, ?int $limit = null): string
    {
        if (! is_scalar($value)) {
            return '';
        }

        $text = trim((string) $value);

        return $limit === null ? $text : mb_substr($text, 0, $limit);
    }

    private function validImageFile(mixed $file): ?UploadedFile
    {
        if (! $file instanceof UploadedFile || ! $file->isValid()) {
            return null;
        }

        $mimeType = strtolower((string) $file->getMimeType());

        return str_starts_with($mimeType, 'image/') && $mimeType !== 'image/svg+xml'
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

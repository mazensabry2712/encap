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
        unset($data['category_ids']);

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
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'distinct', 'exists:categories,id'],
        ]);
    }
}

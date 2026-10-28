<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\EncapImageUpload;
use App\Services\EncapMediaInventory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

final class CompanyController extends Controller
{
    public function index(): View
    {
        return view('admin.companies.index', [
            'companies' => Company::query()->orderBy('sort_order')->orderBy('name_en')->paginate(20),
        ]);
    }

    public function create(EncapMediaInventory $mediaInventory): View
    {
        return view('admin.companies.form', [
            'company' => new Company(),
            'mediaAssets' => $mediaInventory->options(),
        ]);
    }

    public function store(Request $request, EncapImageUpload $imageUpload, EncapMediaInventory $mediaInventory): RedirectResponse
    {
        $data = $this->validated($request, $mediaInventory);
        $libraryImage = $data['library_image'] ?? null;
        $libraryGallery = array_values(array_filter($data['library_gallery'] ?? []));
        unset($data['library_image'], $data['library_gallery'], $data['gallery'], $data['remove_gallery']);
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('image')) {
            $stored = $imageUpload->store($request->file('image'), 'companies', 'image');
            $data['image'] = $stored['path'];
        } elseif ($libraryImage) {
            $data['image'] = $libraryImage;
        } else {
            $data['image'] = null;
        }

        $galleryPaths = $libraryGallery;
        foreach (array_values(array_filter((array) $request->file('gallery', []))) as $file) {
            $stored = $imageUpload->store($file, 'companies/gallery', 'gallery');
            $galleryPaths[] = $stored['path'];
        }
        $data['gallery'] = array_values(array_unique($galleryPaths));

        $company = Company::create($data);

        return redirect()->route('admin.companies.edit', $company)
            ->with('success', __('Company created.'));
    }

    public function edit(Company $company, EncapMediaInventory $mediaInventory): View
    {
        return view('admin.companies.form', [
            'company' => $company,
            'mediaAssets' => $mediaInventory->options(),
        ]);
    }

    public function update(Request $request, Company $company, EncapImageUpload $imageUpload, EncapMediaInventory $mediaInventory): RedirectResponse
    {
        $data = $this->validated($request, $mediaInventory, $company);
        $libraryImage = $data['library_image'] ?? null;
        $libraryGallery = array_values(array_filter($data['library_gallery'] ?? []));
        unset($data['library_image'], $data['library_gallery'], $data['gallery'], $data['remove_gallery']);
        $data['is_published'] = $request->boolean('is_published');

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

        // Do not delete replaced or removed files. The central media library can
        // reuse the same asset elsewhere on the website.
        $data['gallery'] = array_values(array_unique($gallery));
        $company->update($data);

        return redirect()->route('admin.companies.edit', $company)
            ->with('success', __('Company updated.'));
    }

    public function destroy(Company $company): RedirectResponse
    {
        // Keep image files in the central library, even when a company record is removed.
        $company->delete();

        return redirect()->route('admin.companies.index')
            ->with('success', __('Company deleted.'));
    }

    private function validated(Request $request, EncapMediaInventory $mediaInventory, ?Company $company = null): array
    {
        return $request->validate([
            'name_en' => ['required', 'string', 'max:160'],
            'name_ar' => ['nullable', 'string', 'max:160'],
            'slug' => ['required', 'alpha_dash', 'max:180', Rule::unique('companies', 'slug')->ignore($company?->id)],
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
            'gallery' => ['nullable', 'array', 'max:12'],
            'gallery.*' => ['image', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
            'library_gallery' => ['nullable', 'array', 'max:12'],
            'library_gallery.*' => ['string', Rule::in($mediaInventory->paths())],
            'remove_gallery' => ['nullable', 'array'],
            'remove_gallery.*' => ['integer', 'min:0'],
            'is_published' => ['sometimes', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:100000'],
        ]);
    }
}

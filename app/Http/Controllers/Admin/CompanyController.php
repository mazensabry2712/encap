<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\EncapImageUpload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

    public function create(): View
    {
        return view('admin.companies.form', ['company' => new Company()]);
    }

    public function store(Request $request, EncapImageUpload $imageUpload): RedirectResponse
    {
        $data = $this->validated($request);
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('image')) {
            $stored = $imageUpload->store($request->file('image'), 'companies', 'image');
            $data['image'] = $stored['path'];
        }

        $company = Company::create($data);

        return redirect()->route('admin.companies.edit', $company)
            ->with('success', __('Company created.'));
    }

    public function edit(Company $company): View
    {
        return view('admin.companies.form', compact('company'));
    }

    public function update(Request $request, Company $company, EncapImageUpload $imageUpload): RedirectResponse
    {
        $data = $this->validated($request, $company);
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('image')) {
            $stored = $imageUpload->store($request->file('image'), 'companies', 'image');
            $data['image'] = $stored['path'];
        } else {
            unset($data['image']);
        }

        // Image files stay in the central media library; do not delete them when a
        // company changes its main image because another page may still use them.
        $company->update($data);

        return redirect()->route('admin.companies.edit', $company)
            ->with('success', __('Company updated.'));
    }

    public function destroy(Company $company): RedirectResponse
    {
        // Keep the image assets in the central library even when a catalogue record
        // is removed. This avoids deleting a file that may be reused elsewhere.
        $company->delete();

        return redirect()->route('admin.companies.index')
            ->with('success', __('Company deleted.'));
    }

    private function validated(Request $request, ?Company $company = null): array
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
            'is_published' => ['sometimes', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:100000'],
        ]);
    }
}

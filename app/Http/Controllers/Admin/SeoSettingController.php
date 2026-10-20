<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SeoSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.seo.edit', ['settings' => SeoSetting::current()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $settings = SeoSetting::current();
        $data = $request->validate([
            'site_title_en' => ['required', 'string', 'max:255'],
            'site_title_ar' => ['required', 'string', 'max:255'],
            'site_description_en' => ['nullable', 'string', 'max:500'],
            'site_description_ar' => ['nullable', 'string', 'max:500'],
            'organization_name' => ['required', 'string', 'max:190'],
            'organization_url' => ['nullable', 'url', 'max:255'],
            'default_og_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
        ]);

        if ($request->hasFile('default_og_image')) {
            if ($settings->default_og_image) {
                Storage::disk('public')->delete($settings->default_og_image);
            }
            $data['default_og_image'] = $request->file('default_og_image')->store('seo', 'public');
        } else {
            unset($data['default_og_image']);
        }

        $settings->update($data);
        SeoSetting::clearCache();

        return back()->with('success', __('Central SEO settings saved.'));
    }
}

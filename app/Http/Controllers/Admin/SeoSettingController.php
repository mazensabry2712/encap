<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SeoSettingController extends Controller
{
    private const SEO_PAGES = [
        'home', 'about', 'farms', 'packing-stations', 'products', 'quality', 'export', 'contact', 'quote',
    ];

    private const SEO_LOCALES = ['en', 'ar'];

    public function edit(): View
    {
        return view('admin.seo.edit', [
            'settings' => SeoSetting::current(),
            'seoPages' => self::SEO_PAGES,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $settings = SeoSetting::current();
        $rules = [
            'site_title_en' => ['required', 'string', 'max:255'],
            'site_title_ar' => ['required', 'string', 'max:255'],
            'site_description_en' => ['nullable', 'string', 'max:500'],
            'site_description_ar' => ['nullable', 'string', 'max:500'],
            'default_robots' => ['required', Rule::in([
                'index,follow', 'noindex,follow', 'noindex,nofollow', 'index,nofollow',
            ])],
            'organization_name' => ['required', 'string', 'max:190'],
            'organization_url' => ['nullable', 'url', 'max:255'],
            'contact_email' => ['nullable', 'email:rfc', 'max:190'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'whatsapp_number' => ['nullable', 'string', 'max:50'],
            'address_en' => ['nullable', 'string', 'max:255'],
            'address_ar' => ['nullable', 'string', 'max:255'],
            'business_hours_en' => ['nullable', 'string', 'max:255'],
            'business_hours_ar' => ['nullable', 'string', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'youtube_url' => ['nullable', 'url', 'max:255'],
            'default_og_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
        ];

        foreach (self::SEO_PAGES as $page) {
            foreach (self::SEO_LOCALES as $locale) {
                $rules["page_metadata.{$page}.{$locale}.title"] = ['nullable', 'string', 'max:255'];
                $rules["page_metadata.{$page}.{$locale}.description"] = ['nullable', 'string', 'max:500'];
            }
        }

        $data = $request->validate($rules);
        $pageMetadata = [];
        foreach (self::SEO_PAGES as $page) {
            foreach (self::SEO_LOCALES as $locale) {
                $title = data_get($data, "page_metadata.{$page}.{$locale}.title");
                $description = data_get($data, "page_metadata.{$page}.{$locale}.description");

                if (filled($title) || filled($description)) {
                    $pageMetadata[$page][$locale] = [
                        'title' => $title,
                        'description' => $description,
                    ];
                }
            }
        }

        $data['page_metadata'] = $pageMetadata;
        $oldImage = $settings->default_og_image;
        if ($request->hasFile('default_og_image')) {
            $data['default_og_image'] = $request->file('default_og_image')->store('seo', 'public');
        } else {
            unset($data['default_og_image']);
        }

        $settings->fill($data)->save();

        if ($request->hasFile('default_og_image') && $oldImage) {
            Storage::disk('public')->delete($oldImage);
        }

        SeoSetting::clearCache();

        return back()->with('success', __('Central SEO settings saved.'));
    }
}

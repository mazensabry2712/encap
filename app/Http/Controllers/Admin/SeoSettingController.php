<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoSetting;
use App\Services\EncapImageUpload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class SeoSettingController extends Controller
{
    private const SEO_PAGES = [
        'home', 'about', 'farms', 'packing-stations', 'products', 'frozen-products', 'companies', 'quality', 'export', 'contact', 'quote',
    ];

    private const SEO_LOCALES = ['en', 'ar'];
    private const CONTENT_LOCALES = ['en', 'ar', 'fr', 'ru', 'tr'];

    private const PAGE_CONTENT_FIELDS = ['home' => ['label' => 'Home', 'fields' => ['hero_kicker' => ['label' => 'Homepage hero eyebrow', 'type' => 'text', 'max' => 180], 'hero_title' => ['label' => 'Homepage hero headline', 'type' => 'text', 'max' => 255], 'hero_intro' => ['label' => 'Homepage hero introduction', 'type' => 'textarea', 'max' => 1200], 'products_heading' => ['label' => 'Homepage products heading', 'type' => 'text', 'max' => 255], 'products_intro' => ['label' => 'Homepage products introduction', 'type' => 'textarea', 'max' => 1200], 'frozen_heading' => ['label' => 'Frozen section heading', 'type' => 'text', 'max' => 255], 'frozen_intro' => ['label' => 'Frozen section introduction', 'type' => 'textarea', 'max' => 1200]]], 'about' => ['label' => 'About', 'fields' => ['eyebrow' => ['label' => 'Page eyebrow', 'type' => 'text', 'max' => 180], 'title' => ['label' => 'Page headline', 'type' => 'text', 'max' => 255], 'intro' => ['label' => 'Page introduction', 'type' => 'textarea', 'max' => 1500], 'body' => ['label' => 'Main body text', 'type' => 'textarea', 'max' => 3000], 'journey_heading' => ['label' => 'Journey section heading', 'type' => 'text', 'max' => 255], 'journey_intro' => ['label' => 'Journey section introduction', 'type' => 'textarea', 'max' => 1200], 'approach_heading' => ['label' => 'Approach section heading', 'type' => 'text', 'max' => 255], 'approach_intro' => ['label' => 'Approach section introduction', 'type' => 'textarea', 'max' => 1200]]], 'farms' => ['label' => 'Agriculture & Farms', 'fields' => ['eyebrow' => ['label' => 'Page eyebrow', 'type' => 'text', 'max' => 180], 'title' => ['label' => 'Page headline', 'type' => 'text', 'max' => 255], 'intro' => ['label' => 'Page introduction', 'type' => 'textarea', 'max' => 1500], 'body' => ['label' => 'Main body text', 'type' => 'textarea', 'max' => 3000], 'visual_heading' => ['label' => 'Visual section heading', 'type' => 'text', 'max' => 255], 'visual_detail' => ['label' => 'Visual section detail', 'type' => 'textarea', 'max' => 1500], 'services_heading' => ['label' => 'Services section heading', 'type' => 'text', 'max' => 255], 'services_intro' => ['label' => 'Services section introduction', 'type' => 'textarea', 'max' => 1200]]], 'packing-stations' => ['label' => 'Packing Stations', 'fields' => ['eyebrow' => ['label' => 'Page eyebrow', 'type' => 'text', 'max' => 180], 'title' => ['label' => 'Page headline', 'type' => 'text', 'max' => 255], 'intro' => ['label' => 'Page introduction', 'type' => 'textarea', 'max' => 1500], 'body' => ['label' => 'Main body text', 'type' => 'textarea', 'max' => 3000], 'visual_heading' => ['label' => 'Visual section heading', 'type' => 'text', 'max' => 255], 'visual_detail' => ['label' => 'Visual section detail', 'type' => 'textarea', 'max' => 1500], 'services_heading' => ['label' => 'Services section heading', 'type' => 'text', 'max' => 255], 'services_intro' => ['label' => 'Services section introduction', 'type' => 'textarea', 'max' => 1200]]], 'quality' => ['label' => 'Quality & Processing', 'fields' => ['eyebrow' => ['label' => 'Page eyebrow', 'type' => 'text', 'max' => 180], 'title' => ['label' => 'Page headline', 'type' => 'text', 'max' => 255], 'intro' => ['label' => 'Page introduction', 'type' => 'textarea', 'max' => 1500], 'body' => ['label' => 'Main body text', 'type' => 'textarea', 'max' => 3000], 'visual_heading' => ['label' => 'Visual section heading', 'type' => 'text', 'max' => 255], 'visual_detail' => ['label' => 'Visual section detail', 'type' => 'textarea', 'max' => 1500], 'services_heading' => ['label' => 'Services section heading', 'type' => 'text', 'max' => 255], 'services_intro' => ['label' => 'Services section introduction', 'type' => 'textarea', 'max' => 1200], 'frameworks_heading' => ['label' => 'Quality frameworks heading', 'type' => 'text', 'max' => 255], 'frameworks_intro' => ['label' => 'Quality frameworks introduction', 'type' => 'textarea', 'max' => 1200], 'frameworks_note' => ['label' => 'Quality frameworks note', 'type' => 'textarea', 'max' => 1000]]], 'export' => ['label' => 'Export & Logistics', 'fields' => ['eyebrow' => ['label' => 'Page eyebrow', 'type' => 'text', 'max' => 180], 'title' => ['label' => 'Page headline', 'type' => 'text', 'max' => 255], 'intro' => ['label' => 'Page introduction', 'type' => 'textarea', 'max' => 1500], 'body' => ['label' => 'Main body text', 'type' => 'textarea', 'max' => 3000], 'visual_heading' => ['label' => 'Visual section heading', 'type' => 'text', 'max' => 255], 'visual_detail' => ['label' => 'Visual section detail', 'type' => 'textarea', 'max' => 1500], 'services_heading' => ['label' => 'Services section heading', 'type' => 'text', 'max' => 255], 'services_intro' => ['label' => 'Services section introduction', 'type' => 'textarea', 'max' => 1200]]], 'products' => ['label' => 'Products', 'fields' => ['title' => ['label' => 'Page headline', 'type' => 'text', 'max' => 255], 'hero_tagline' => ['label' => 'Catalogue hero tagline', 'type' => 'text', 'max' => 255], 'hero_description' => ['label' => 'Catalogue hero introduction', 'type' => 'textarea', 'max' => 1200], 'section_heading' => ['label' => 'Catalogue section heading', 'type' => 'text', 'max' => 255], 'section_intro' => ['label' => 'Catalogue section introduction', 'type' => 'textarea', 'max' => 1200]]], 'frozen-products' => ['label' => 'Frozen fruit & vegetables', 'fields' => ['title' => ['label' => 'Page headline', 'type' => 'text', 'max' => 255], 'hero_tagline' => ['label' => 'Catalogue hero tagline', 'type' => 'text', 'max' => 255], 'hero_description' => ['label' => 'Catalogue hero introduction', 'type' => 'textarea', 'max' => 1200], 'section_heading' => ['label' => 'Catalogue section heading', 'type' => 'text', 'max' => 255], 'section_intro' => ['label' => 'Catalogue section introduction', 'type' => 'textarea', 'max' => 1200]]], 'companies' => ['label' => 'Our Companies', 'fields' => ['title' => ['label' => 'Company directory heading', 'type' => 'text', 'max' => 255], 'hero_tagline' => ['label' => 'Company directory tagline', 'type' => 'text', 'max' => 255], 'hero_description' => ['label' => 'Company directory introduction', 'type' => 'textarea', 'max' => 1200]]], 'contact' => ['label' => 'Contact', 'fields' => ['title' => ['label' => 'Contact page heading', 'type' => 'text', 'max' => 255], 'intro' => ['label' => 'Contact page introduction', 'type' => 'textarea', 'max' => 1200]]], 'quote' => ['label' => 'Request a Quote', 'fields' => ['title' => ['label' => 'Quote page heading', 'type' => 'text', 'max' => 255], 'intro' => ['label' => 'Quote page introduction', 'type' => 'textarea', 'max' => 1200]]]];

    public function edit(): View
    {
        return view('admin.seo.edit', [
            'settings' => SeoSetting::current(),
            'seoPages' => self::SEO_PAGES,
            'contentPages' => self::PAGE_CONTENT_FIELDS,
            'contentLocales' => ['en' => 'English', 'ar' => 'Arabic', 'fr' => 'French', 'ru' => 'Russian', 'tr' => 'Turkish'],
        ]);
    }

    public function update(Request $request, EncapImageUpload $imageUpload): RedirectResponse
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

        foreach (self::PAGE_CONTENT_FIELDS as $page => $pageConfig) {
            foreach (self::CONTENT_LOCALES as $locale) {
                foreach ($pageConfig['fields'] as $field => $config) {
                    $rules["page_content.{$page}.{$locale}.{$field}"] = ['nullable', 'string', 'max:' . $config['max']];
                }
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
        $pageContent = [];
        foreach (self::PAGE_CONTENT_FIELDS as $page => $pageConfig) {
            foreach (self::CONTENT_LOCALES as $locale) {
                foreach ($pageConfig['fields'] as $field => $config) {
                    $value = data_get($data, "page_content.{$page}.{$locale}.{$field}");
                    if (filled($value)) $pageContent[$page][$locale][$field] = trim($value);
                }
            }
        }
        $data['page_content'] = $pageContent;
        $storedImage = null;

        if ($request->hasFile('default_og_image')) {
            $storedImage = $imageUpload->store(
                $request->file('default_og_image'),
                'seo',
                'default_og_image'
            );
            $data['default_og_image'] = $storedImage['path'];
        } else {
            unset($data['default_og_image']);
        }

        try {
            $settings->fill($data)->save();
        } catch (Throwable $exception) {
            if ($storedImage) {
                Storage::disk($storedImage['disk'])->delete($storedImage['path']);
            }

            throw $exception;
        }

        // Retain replaced SEO images in the central media library so other pages
        // or future settings can reuse them without losing shared assets.

        SeoSetting::clearCache();

        return back()
            ->with('success', __('Central SEO settings saved.'))
            ->with('image_storage_fallback', $storedImage !== null && $storedImage['disk'] === 'public');
    }
}

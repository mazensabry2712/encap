<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Throwable;

final class EncapImage
{
    /**
     * Resolve an image URL and keep legacy/fallback uploads reachable.
     */
    public static function url(string $path, ?string $fallbackUrl = null): string
    {
        $path = ltrim($path, '/');

        try {
            $images = Storage::disk('encap_images');

            if ($images->exists($path)) {
                return self::versionedUrl($images->url($path), $images, $path);
            }
        } catch (Throwable) {
            // Keep the public disk and configured fallback URL available if this disk is broken.
        }

        try {
            $legacy = Storage::disk('public');
            if ($legacy->exists($path)) {
                return self::versionedUrl(self::encapUrl($path), $legacy, $path);
            }
        } catch (Throwable) {
            // Use the supplied fallback URL when neither disk can be inspected.
        }

        return $fallbackUrl ?? self::encapUrl($path);
    }

    private static function encapUrl(string $path): string
    {
        $baseUrl = config('filesystems.disks.encap_images.url');

        if (! is_string($baseUrl) || $baseUrl === '') {
            $baseUrl = url('/encap-img');
        }

        return rtrim($baseUrl, '/').'/'.$path;
    }
    /**
     * Resolve one named website image slot. Different page sections can start
     * from the same library photo while remaining independently replaceable.
     */
    public static function slot(string $key, string $defaultPath, ?string $fallbackUrl = null): string
    {
        try {
            $slots = SeoSetting::current()->site_media_slots ?? [];
            $path = data_get($slots, $key);
        } catch (Throwable) {
            $path = null;
        }

        if (! is_string($path) || $path === '') {
            $path = $defaultPath;
        }

        return self::url($path, $fallbackUrl);
    }

    /**
     * Fixed image locations rendered by Blade templates.
     *
     * @return array<string, array{path: string, en: string, ar: string}>
     */
    public static function defaultSlots(): array
    {
        return [
            'home.hero_background' => ['path' => 'orange-orchard-hero-2026.jpg', 'en' => 'Homepage — hero background', 'ar' => 'الرئيسية — خلفية الواجهة'],
            'home.hero_feature' => ['path' => 'produce-mix-2026.jpg', 'en' => 'Homepage — featured produce photo', 'ar' => 'الرئيسية — صورة المنتجات البارزة'],
            'home.service_farms' => ['path' => 'citrus-orchard-2026.jpg', 'en' => 'Homepage — farms service card', 'ar' => 'الرئيسية — بطاقة خدمة المزارع'],
            'home.service_packing' => ['path' => 'produce-packing-2026.jpg', 'en' => 'Homepage — packing service card', 'ar' => 'الرئيسية — بطاقة خدمة التعبئة'],
            'home.service_export' => ['path' => 'cargo-port-2026.jpg', 'en' => 'Homepage — export service card', 'ar' => 'الرئيسية — بطاقة خدمة التصدير'],
            'home.story' => ['path' => 'farm-cultivation-2026.jpg', 'en' => 'Homepage — company story photo', 'ar' => 'الرئيسية — صورة قصة الشركة'],
            'home.frozen_feature' => ['path' => 'frozen-berries-2026.jpg', 'en' => 'Homepage — frozen products feature', 'ar' => 'الرئيسية — صورة قسم المنتجات المجمدة'],
            'home.quality_band' => ['path' => 'produce-mix-2026.jpg', 'en' => 'Homepage — quality section background', 'ar' => 'الرئيسية — خلفية قسم الجودة'],
            'products.hero_primary' => ['path' => 'citrus-orchard-2026.jpg', 'en' => 'Products — primary hero photo', 'ar' => 'المنتجات — الصورة الرئيسية'],
            'products.hero_secondary' => ['path' => 'produce-mix-2026.jpg', 'en' => 'Products — secondary hero photo', 'ar' => 'المنتجات — الصورة الثانوية'],
            'products.hero_tertiary' => ['path' => 'farm-cultivation-2026.jpg', 'en' => 'Products — tertiary hero photo', 'ar' => 'المنتجات — الصورة الثالثة'],
            'products.cta' => ['path' => 'produce-mix-2026.jpg', 'en' => 'Products — enquiry section photo', 'ar' => 'المنتجات — صورة قسم الاستفسار'],
            'frozen.hero_primary' => ['path' => 'frozen-berries-2026.jpg', 'en' => 'Frozen catalogue — primary hero photo', 'ar' => 'كتالوج المجمدات — الصورة الرئيسية'],
            'frozen.hero_secondary' => ['path' => 'produce-mix-2026.jpg', 'en' => 'Frozen catalogue — secondary hero photo', 'ar' => 'كتالوج المجمدات — الصورة الثانوية'],
            'frozen.hero_tertiary' => ['path' => 'farm-cultivation-2026.jpg', 'en' => 'Frozen catalogue — tertiary hero photo', 'ar' => 'كتالوج المجمدات — الصورة الثالثة'],
            'frozen.cta' => ['path' => 'produce-mix-2026.jpg', 'en' => 'Frozen catalogue — enquiry section photo', 'ar' => 'كتالوج المجمدات — صورة قسم الاستفسار'],
            'content.about.hero_landscape' => ['path' => 'citrus-orchard-2026.jpg', 'en' => 'About — landscape hero photo', 'ar' => 'من نحن — صورة الواجهة'],
            'content.about.hero_produce' => ['path' => 'produce-mix-2026.jpg', 'en' => 'About — produce hero photo', 'ar' => 'من نحن — صورة المنتجات'],
            'content.about.approach' => ['path' => 'farm-cultivation-2026.jpg', 'en' => 'About — approach section photo', 'ar' => 'من نحن — صورة قسم نهج العمل'],
            'content.farms.hero_landscape' => ['path' => 'citrus-orchard-2026.jpg', 'en' => 'Farms — landscape hero photo', 'ar' => 'المزارع — صورة الواجهة'],
            'content.farms.hero_produce' => ['path' => 'produce-mix-2026.jpg', 'en' => 'Farms — produce hero photo', 'ar' => 'المزارع — صورة المنتجات'],
            'content.farms.approach' => ['path' => 'farm-cultivation-2026.jpg', 'en' => 'Farms — approach section photo', 'ar' => 'المزارع — صورة قسم نهج العمل'],
            'content.packing-stations.hero_landscape' => ['path' => 'citrus-orchard-2026.jpg', 'en' => 'Packing — landscape hero photo', 'ar' => 'التعبئة — صورة الواجهة'],
            'content.packing-stations.hero_produce' => ['path' => 'produce-mix-2026.jpg', 'en' => 'Packing — produce hero photo', 'ar' => 'التعبئة — صورة المنتجات'],
            'content.packing-stations.approach' => ['path' => 'farm-cultivation-2026.jpg', 'en' => 'Packing — approach section photo', 'ar' => 'التعبئة — صورة قسم نهج العمل'],
            'content.quality.hero_landscape' => ['path' => 'citrus-orchard-2026.jpg', 'en' => 'Quality — landscape hero photo', 'ar' => 'الجودة — صورة الواجهة'],
            'content.quality.hero_produce' => ['path' => 'produce-mix-2026.jpg', 'en' => 'Quality — produce hero photo', 'ar' => 'الجودة — صورة المنتجات'],
            'content.quality.approach' => ['path' => 'farm-cultivation-2026.jpg', 'en' => 'Quality — approach section photo', 'ar' => 'الجودة — صورة قسم نهج العمل'],
            'content.export.hero_landscape' => ['path' => 'citrus-orchard-2026.jpg', 'en' => 'Export — landscape hero photo', 'ar' => 'التصدير — صورة الواجهة'],
            'content.export.hero_produce' => ['path' => 'produce-mix-2026.jpg', 'en' => 'Export — produce hero photo', 'ar' => 'التصدير — صورة المنتجات'],
            'content.export.approach' => ['path' => 'farm-cultivation-2026.jpg', 'en' => 'Export — approach section photo', 'ar' => 'التصدير — صورة قسم نهج العمل'],
        ];
    }

    /**
     * Give replaced assets a new URL so browsers cannot keep showing stale bytes.
     */
    private static function versionedUrl(string $url, $disk, string $path): string
    {
        try {
            $absolutePath = $disk->path($path);
            clearstatcache(true, $absolutePath);
            $version = $disk->lastModified($path).'-'.$disk->size($path);

            return $url.(str_contains($url, '?') ? '&' : '?').'v='.rawurlencode((string) $version);
        } catch (Throwable) {
            return $url;
        }
    }

}

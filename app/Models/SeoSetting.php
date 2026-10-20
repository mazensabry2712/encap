<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SeoSetting extends Model
{
    protected $fillable = [
        'site_title_en', 'site_title_ar', 'site_description_en', 'site_description_ar',
        'default_og_image', 'organization_name', 'organization_url',
    ];

    public static function current(): self
    {
        return Cache::remember('encap.seo-settings', 300, fn () => static::query()->firstOrCreate(
            ['id' => 1],
            [
                'site_title_en' => 'ENCAP | From Our Farms to Global Markets',
                'site_title_ar' => 'ENCAP | من مزارعنا إلى الأسواق العالمية',
                'site_description_en' => 'Agricultural production, sourcing, processing and export from Egypt to global markets.',
                'site_description_ar' => 'إنتاج وتوريد وتجهيز وتصدير المنتجات الزراعية من مصر إلى الأسواق العالمية.',
                'organization_name' => 'ENCAP',
            ]
        ));
    }

    public static function clearCache(): void
    {
        Cache::forget('encap.seo-settings');
    }
}

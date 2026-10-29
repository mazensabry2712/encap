<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SeoSetting extends Model
{
    protected $fillable = [
        'site_title_en', 'site_title_ar', 'site_description_en', 'site_description_ar',
        'default_og_image', 'default_robots', 'organization_name', 'organization_url',
        'contact_email', 'contact_phone', 'whatsapp_number', 'address_en', 'address_ar',
        'business_hours_en', 'business_hours_ar', 'facebook_url', 'instagram_url',
        'linkedin_url', 'youtube_url', 'page_metadata', 'page_content',
    ];

    protected $attributes = [
        'site_title_en' => 'ENCAP | From Our Farms to Global Markets',
        'site_title_ar' => 'ENCAP | من مزارعنا إلى الأسواق العالمية',
        'site_description_en' => 'Agricultural production, sourcing, processing and export from Egypt to global markets.',
        'site_description_ar' => 'إنتاج وتوريد وتجهيز وتصدير المنتجات الزراعية من مصر إلى الأسواق العالمية.',
        'default_robots' => 'index,follow',
        'organization_name' => 'ENCAP',
    ];

    protected function casts(): array
    {
        return [
            'page_metadata' => 'array',
            'page_content' => 'array',
        ];
    }

    public static function current(): self
    {
        $key = 'encap.seo-settings';
        $cached = Cache::get($key);

        // Discard legacy cached objects to avoid incomplete-class errors.
        if (!is_array($cached)) {
            if ($cached !== null) {
                Cache::forget($key);
            }

            $cached = Cache::remember(
                $key,
                300,
                fn (): array => (static::query()->first() ?? new static())->getAttributes()
            );
        }

        // Restore raw attributes, including the primary key, without losing the record ID.
        $settings = new static();
        $settings->setRawAttributes($cached, true);

        return $settings;
    }

    /**
     * Resolve page metadata without requiring a separate SEO record per page.
     *
     * @return array{title: string, description: string}
     */
    public function metadataFor(
        string $page,
        string $locale,
        string $fallbackTitle,
        string $fallbackDescription
    ): array {
        $language = $locale === 'ar' ? 'ar' : 'en';
        $metadata = $this->page_metadata[$page][$language] ?? [];

        return [
            'title' => filled($metadata['title'] ?? null) ? $metadata['title'] : $fallbackTitle,
            'description' => filled($metadata['description'] ?? null)
                ? $metadata['description']
                : $fallbackDescription,
        ];
    }

    public static function clearCache(): void
    {
        Cache::forget('encap.seo-settings');
    }
}

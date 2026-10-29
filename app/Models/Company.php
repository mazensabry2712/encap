<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Company extends Model
{
    protected $fillable = [
        'name_en', 'name_ar', 'slug',
        'short_description_en', 'short_description_ar',
        'description_en', 'description_ar',
        'country_en', 'country_ar', 'location_en', 'location_ar',
        'website_url', 'image', 'gallery', 'content_sections', 'is_published', 'sort_order',
    ];

    /**
     * Initial page content shown until an administrator saves custom sections.
     *
     * @return list<array{title_ar: string, title_en: string, images: list<string>}>
     */
    public static function defaultContentSections(): array
    {
        return [
            [
                'title_ar' => 'زراعة وإنتاج البطاطس',
                'title_en' => 'Potato cultivation and production',
                'images' => ['product-potatoes-2026.jpg', 'potato-sorting-2026.jpg'],
            ],
            [
                'title_ar' => 'تجهيز الأراضي الزراعية وشبكات الري والبنية التحتية',
                'title_en' => 'Agricultural land preparation, irrigation networks and infrastructure',
                'images' => ['farm-cultivation-2026.jpg', 'orchard-harvest-2026.jpg'],
            ],
        ];
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'company_products')->withTimestamps();
    }

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'content_sections' => 'array',
            'is_published' => 'boolean',
        ];
    }
}

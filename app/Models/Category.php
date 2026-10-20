<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name_en', 'name_ar', 'slug', 'short_description_en', 'short_description_ar',
        'description_en', 'description_ar', 'image', 'gallery', 'available_forms',
        'packaging_en', 'packaging_ar', 'is_published', 'sort_order',
        'seo_title_en', 'seo_title_ar', 'seo_description_en', 'seo_description_ar',
    ];

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'available_forms' => 'array',
            'is_published' => 'boolean',
        ];
    }

    public function quoteRequests(): HasMany
    {
        return $this->hasMany(QuoteRequest::class);
    }

    public function getLocalizedAttribute(string $field): mixed
    {
        $locale = app()->getLocale();
        return $this->{$field . '_' . ($locale === 'ar' ? 'ar' : 'en')}
            ?: $this->{$field . '_en'};
    }
}

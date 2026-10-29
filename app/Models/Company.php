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
        'website_url', 'image', 'gallery', 'is_published', 'sort_order',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'company_products')->withTimestamps();
    }

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'is_published' => 'boolean',
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name_en', 'name_ar', 'slug', 'short_description_en', 'short_description_ar',
        'description_en', 'description_ar', 'image', 'gallery', 'available_forms',
        'varieties_en', 'varieties_ar', 'harvest_window_en', 'harvest_window_ar',
        'specifications_en', 'specifications_ar', 'packaging_en', 'packaging_ar', 'is_published', 'sort_order',
        'seo_title_en', 'seo_title_ar', 'seo_description_en', 'seo_description_ar',
        'overview_heading_en', 'overview_heading_ar', 'enquiry_note_heading_en', 'enquiry_note_heading_ar',
        'enquiry_note_text_en', 'enquiry_note_text_ar', 'bottom_cta_heading_en', 'bottom_cta_heading_ar',
        'bottom_cta_text_en', 'bottom_cta_text_ar', 'show_in_catalogue', 'show_in_frozen_catalogue', 'show_on_homepage',
        'show_product_images', 'show_enquiry_note',
        'show_description', 'show_available_forms', 'show_packaging', 'show_specifications', 'show_bottom_cta',
    ];

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'available_forms' => 'array',
            'is_published' => 'boolean',
            'show_in_catalogue' => 'boolean',
            'show_in_frozen_catalogue' => 'boolean',
            'show_on_homepage' => 'boolean',
            'show_product_images' => 'boolean',
            'show_enquiry_note' => 'boolean',
            'show_description' => 'boolean',
            'show_available_forms' => 'boolean',
            'show_packaging' => 'boolean',
            'show_specifications' => 'boolean',
            'show_bottom_cta' => 'boolean',
        ];
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_products')->withTimestamps();
    }

    public function quoteRequests(): HasMany
    {
        return $this->hasMany(QuoteRequest::class);
    }
}

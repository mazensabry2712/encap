<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteRequest extends Model
{
    protected $fillable = [
        'category_id', 'contact_name', 'company_name', 'email', 'country',
        'quantity', 'quantity_unit', 'product_form', 'packaging_requirements',
        'message', 'status',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}

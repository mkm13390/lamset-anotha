<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturnPolicy extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'return_window_days',
        'exchange_window_days',
        'allow_refund',
        'allow_exchange',
        'allow_store_credit',
        'customer_pays_return_shipping',
        'require_original_packaging',
        'excluded_category_ids',
        'excluded_product_ids',
        'terms_ar',
        'terms_en',
        'is_active',
    ];

    protected $casts = [
        'return_window_days' => 'integer',
        'exchange_window_days' => 'integer',
        'allow_refund' => 'boolean',
        'allow_exchange' => 'boolean',
        'allow_store_credit' => 'boolean',
        'customer_pays_return_shipping' => 'boolean',
        'require_original_packaging' => 'boolean',
        'excluded_category_ids' => 'array',
        'excluded_product_ids' => 'array',
        'is_active' => 'boolean',
    ];

    public function returnRequests(): HasMany
    {
        return $this->hasMany(ReturnRequest::class);
    }
}

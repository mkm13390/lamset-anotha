<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductBundle extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'slug',
        'pricing_type',
        'value',
        'is_active',
        'starts_at',
        'ends_at',
        'description_ar',
        'description_en',
    ];

    protected $casts = [
        'value' => 'decimal:3',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(ProductBundleItem::class);
    }

    public function isAvailableNow(): bool
    {
        return $this->is_active
            && (!$this->starts_at || $this->starts_at->lte(now()))
            && (!$this->ends_at || $this->ends_at->gte(now()));
    }
}

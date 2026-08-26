<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promotion extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'code',
        'promotion_type',
        'discount_type',
        'discount_value',
        'minimum_spend',
        'minimum_quantity',
        'usage_limit',
        'per_customer_limit',
        'is_stackable',
        'is_active',
        'starts_at',
        'ends_at',
        'conditions',
        'actions',
    ];

    protected $casts = [
        'discount_value' => 'decimal:3',
        'minimum_spend' => 'decimal:3',
        'minimum_quantity' => 'integer',
        'usage_limit' => 'integer',
        'per_customer_limit' => 'integer',
        'is_stackable' => 'boolean',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'conditions' => 'array',
        'actions' => 'array',
    ];

    public function usages(): HasMany
    {
        return $this->hasMany(PromotionUsage::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(MarketingCampaign::class);
    }

    public function isActiveNow(): bool
    {
        return $this->is_active
            && (!$this->starts_at || $this->starts_at->lte(now()))
            && (!$this->ends_at || $this->ends_at->gte(now()));
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyTier extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'code',
        'min_spend',
        'min_points',
        'points_multiplier',
        'discount_percent',
        'free_shipping',
        'early_access',
        'is_active',
        'sort_order',
        'benefits',
    ];

    protected $casts = [
        'min_spend' => 'decimal:3',
        'min_points' => 'integer',
        'points_multiplier' => 'decimal:3',
        'discount_percent' => 'decimal:3',
        'free_shipping' => 'boolean',
        'early_access' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'benefits' => 'array',
    ];

    public function loyaltyProfiles(): HasMany
    {
        return $this->hasMany(CustomerLoyaltyProfile::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerLoyaltyProfile extends Model
{
    protected $fillable = [
        'user_id',
        'loyalty_tier_id',
        'points_balance',
        'lifetime_points',
        'lifetime_spend',
        'store_credit_balance',
        'referral_code',
        'tier_updated_at',
        'last_purchase_at',
    ];

    protected $casts = [
        'points_balance' => 'integer',
        'lifetime_points' => 'integer',
        'lifetime_spend' => 'decimal:3',
        'store_credit_balance' => 'decimal:3',
        'tier_updated_at' => 'datetime',
        'last_purchase_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tier(): BelongsTo
    {
        return $this->belongsTo(LoyaltyTier::class, 'loyalty_tier_id');
    }

    public function storeCreditTransactions(): HasMany
    {
        return $this->hasMany(
            StoreCreditTransaction::class,
            'user_id',
            'user_id'
        );
    }
}

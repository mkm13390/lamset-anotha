<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromotionUsage extends Model
{
    protected $fillable = [
        'promotion_id',
        'user_id',
        'order_id',
        'pos_sale_id',
        'discount_amount',
        'reward_value',
        'used_at',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:3',
        'reward_value' => 'decimal:3',
        'used_at' => 'datetime',
    ];

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function posSale(): BelongsTo
    {
        return $this->belongsTo(PosSale::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    protected $fillable = [
        'referrer_user_id',
        'referred_user_id',
        'referral_code',
        'status',
        'order_value',
        'referrer_reward',
        'referred_reward',
        'order_id',
        'qualified_at',
        'rewarded_at',
    ];

    protected $casts = [
        'order_value' => 'decimal:3',
        'referrer_reward' => 'decimal:3',
        'referred_reward' => 'decimal:3',
        'qualified_at' => 'datetime',
        'rewarded_at' => 'datetime',
    ];

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_user_id');
    }

    public function referredUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}

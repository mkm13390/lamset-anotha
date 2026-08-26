<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyAccount extends Model
{
    protected $fillable = [
        'user_id',
        'points_balance',
        'total_points_earned',
        'total_points_redeemed',
    ];

    protected $casts = [
        'points_balance' => 'integer',
        'total_points_earned' => 'integer',
        'total_points_redeemed' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AbandonedCart extends Model
{
    protected $fillable = [
        'session_key',
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'subtotal',
        'total',
        'status',
        'last_activity_at',
        'abandoned_at',
        'recovered_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:3',
        'total' => 'decimal:3',
        'last_activity_at' => 'datetime',
        'abandoned_at' => 'datetime',
        'recovered_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(AbandonedCartItem::class);
    }
}

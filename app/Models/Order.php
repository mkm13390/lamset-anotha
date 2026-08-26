<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'email',
        'governorate',
        'wilayat',
        'address',
        'notes',
        'delivery_method',
        'shipping_fee',
        'payment_method',
        'payment_status',
        'status',
        'currency',
        'subtotal',
        'discount_amount',
        'coupon_code',
        'total',
        'paid_at',
    ];

    protected $casts = [
        'shipping_fee' => 'decimal:3',
        'subtotal' => 'decimal:3',
        'discount_amount' => 'decimal:3',
        'total' => 'decimal:3',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}

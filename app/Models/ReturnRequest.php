<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturnRequest extends Model
{
    protected $fillable = [
        'return_number',
        'order_id',
        'user_id',
        'return_policy_id',
        'replacement_order_id',
        'request_type',
        'status',
        'reason_code',
        'customer_notes',
        'admin_notes',
        'requested_amount',
        'approved_amount',
        'return_shipping_cost',
        'reviewed_by',
        'requested_at',
        'reviewed_at',
        'received_at',
        'completed_at',
    ];

    protected $casts = [
        'requested_amount' => 'decimal:3',
        'approved_amount' => 'decimal:3',
        'return_shipping_cost' => 'decimal:3',
        'requested_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'received_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function policy(): BelongsTo
    {
        return $this->belongsTo(ReturnPolicy::class, 'return_policy_id');
    }

    public function replacementOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'replacement_order_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReturnRequestItem::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(ReturnShipment::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }
}

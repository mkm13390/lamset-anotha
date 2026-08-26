<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnRequestItem extends Model
{
    protected $fillable = [
        'return_request_id',
        'order_item_id',
        'quantity',
        'condition',
        'reason_code',
        'notes',
        'unit_refund_amount',
        'approved_refund_amount',
        'restock',
        'mark_damaged',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_refund_amount' => 'decimal:3',
        'approved_refund_amount' => 'decimal:3',
        'restock' => 'boolean',
        'mark_damaged' => 'boolean',
    ];

    public function returnRequest(): BelongsTo
    {
        return $this->belongsTo(ReturnRequest::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }
}

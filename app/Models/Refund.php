<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    protected $fillable = [
        'refund_number',
        'return_request_id',
        'payment_transaction_id',
        'user_id',
        'processed_by',
        'refund_method',
        'status',
        'amount',
        'currency',
        'external_refund_id',
        'reference',
        'notes',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:3',
        'processed_at' => 'datetime',
    ];

    public function returnRequest(): BelongsTo
    {
        return $this->belongsTo(ReturnRequest::class);
    }

    public function paymentTransaction(): BelongsTo
    {
        return $this->belongsTo(PaymentTransaction::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}

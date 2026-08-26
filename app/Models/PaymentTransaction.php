<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'transaction_number',
        'payment_gateway_id',
        'user_id',
        'payable_type',
        'payable_id',
        'type',
        'status',
        'amount',
        'refunded_amount',
        'currency',
        'external_transaction_id',
        'approval_code',
        'bank_reference',
        'rrn',
        'card_scheme',
        'masked_card',
        'authorized_at',
        'paid_at',
        'failed_at',
        'request_payload',
        'response_payload',
        'failure_code',
        'failure_message',
    ];

    protected $casts = [
        'amount' => 'decimal:3',
        'refunded_amount' => 'decimal:3',
        'authorized_at' => 'datetime',
        'paid_at' => 'datetime',
        'failed_at' => 'datetime',
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(PaymentGateway::class, 'payment_gateway_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function getRemainingRefundableAttribute(): float
    {
        return max(
            round(
                (float) $this->amount - (float) $this->refunded_amount,
                3
            ),
            0
        );
    }
}

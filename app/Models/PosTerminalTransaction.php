<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosTerminalTransaction extends Model
{
    protected $fillable = [
        'transaction_number',
        'payment_terminal_id',
        'pos_sale_id',
        'pos_sale_payment_id',
        'pos_shift_id',
        'user_id',
        'transaction_type',
        'amount',
        'status',
        'approval_code',
        'bank_reference',
        'rrn',
        'masked_card',
        'card_scheme',
        'failure_reason',
        'request_payload',
        'response_payload',
        'sent_at',
        'completed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:3',
        'request_payload' => 'array',
        'response_payload' => 'array',
        'sent_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function terminal(): BelongsTo
    {
        return $this->belongsTo(PaymentTerminal::class, 'payment_terminal_id');
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(PosSale::class, 'pos_sale_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(PosSalePayment::class, 'pos_sale_payment_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(PosShift::class, 'pos_shift_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isFinal(): bool
    {
        return in_array($this->status, [
            'approved',
            'declined',
            'cancelled',
            'timeout',
            'error',
        ], true);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}

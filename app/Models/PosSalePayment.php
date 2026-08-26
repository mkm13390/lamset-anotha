<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosSalePayment extends Model
{
    protected $fillable = [
        'pos_sale_id',
        'cash_register_id',
        'pos_shift_id',
        'user_id',
        'payment_method',
        'amount',
        'refunded_amount',
        'tendered_amount',
        'change_amount',
        'reference',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:3',
        'refunded_amount' => 'decimal:3',
        'tendered_amount' => 'decimal:3',
        'change_amount' => 'decimal:3',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(PosSale::class, 'pos_sale_id');
    }

    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(PosShift::class, 'pos_shift_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function terminalTransactions(): HasMany
    {
        return $this->hasMany(
            PosTerminalTransaction::class,
            'pos_sale_payment_id'
        );
    }

    public function getRemainingRefundableAmountAttribute(): float
    {
        return max(
            round(
                (float) $this->amount
                - (float) $this->refunded_amount,
                3
            ),
            0
        );
    }
}

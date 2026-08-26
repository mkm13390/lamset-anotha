<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosShift extends Model
{
    protected $fillable = [
        'cash_register_id',
        'user_id',
        'status',
        'opening_cash',
        'cash_sales',
        'cash_refunds',
        'cash_in',
        'cash_out',
        'expected_cash',
        'closing_cash',
        'difference_amount',
        'opened_at',
        'closed_at',
        'opening_notes',
        'closing_notes',
    ];

    protected $casts = [
        'opening_cash' => 'decimal:3',
        'cash_sales' => 'decimal:3',
        'cash_refunds' => 'decimal:3',
        'cash_in' => 'decimal:3',
        'cash_out' => 'decimal:3',
        'expected_cash' => 'decimal:3',
        'closing_cash' => 'decimal:3',
        'difference_amount' => 'decimal:3',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(PosSale::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PosSalePayment::class);
    }

    public function heldSales(): HasMany
    {
        return $this->hasMany(PosHeldSale::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function recalculateExpectedCash(): void
    {
        $expected = round(
            (float) $this->opening_cash
            + (float) $this->cash_sales
            - (float) $this->cash_refunds
            + (float) $this->cash_in
            - (float) $this->cash_out,
            3
        );

        $this->update([
            'expected_cash' => $expected,
        ]);
    }
}

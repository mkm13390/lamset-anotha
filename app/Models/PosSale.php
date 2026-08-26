<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosSale extends Model
{
    protected $fillable = [
        'sale_number',
        'receipt_number',
        'cash_register_id',
        'user_id',
        'customer_id',
        'pos_shift_id',
        'exchange_parent_sale_id',
        'customer_name',
        'customer_phone',
        'subtotal',
        'discount_amount',
        'total',
        'payment_method',
        'cash_received',
        'change_due',
        'receipt_print_count',
        'status',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:3',
        'discount_amount' => 'decimal:3',
        'total' => 'decimal:3',
        'cash_received' => 'decimal:3',
        'change_due' => 'decimal:3',
        'receipt_print_count' => 'integer',
        'completed_at' => 'datetime',
    ];

    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(PosShift::class, 'pos_shift_id');
    }

    public function exchangeParentSale(): BelongsTo
    {
        return $this->belongsTo(PosSale::class, 'exchange_parent_sale_id');
    }

    public function exchangeSales(): HasMany
    {
        return $this->hasMany(PosSale::class, 'exchange_parent_sale_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PosSaleItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PosSalePayment::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(PosReturn::class);
    }

    public function terminalTransactions(): HasMany
    {
        return $this->hasMany(PosTerminalTransaction::class);
    }

    public function getPaidAmountAttribute(): float
    {
        return round((float) $this->payments()->sum('amount'), 3);
    }

    public function getRefundedAmountAttribute(): float
    {
        return round((float) $this->returns()->sum('refund_amount'), 3);
    }
}

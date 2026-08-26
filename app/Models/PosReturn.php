<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosReturn extends Model
{
    protected $fillable = [
        'return_number',
        'pos_sale_id',
        'cash_register_id',
        'pos_shift_id',
        'user_id',
        'return_type',
        'refund_method',
        'subtotal',
        'deduction_amount',
        'refund_amount',
        'reason',
        'reference',
        'notes',
        'returned_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:3',
        'deduction_amount' => 'decimal:3',
        'refund_amount' => 'decimal:3',
        'returned_at' => 'datetime',
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

    public function items(): HasMany
    {
        return $this->hasMany(PosReturnItem::class);
    }
}

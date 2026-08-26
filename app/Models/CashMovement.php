<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashMovement extends Model
{
    protected $fillable = [
        'cash_register_id',
        'user_id',
        'type',
        'amount',
        'balance_after',
        'reference',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:3',
        'balance_after' => 'decimal:3',
    ];

    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

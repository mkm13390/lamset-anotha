<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashRegister extends Model
{
    protected $fillable = [
        'name',
        'opening_balance',
        'current_balance',
        'is_active',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:3',
        'current_balance' => 'decimal:3',
        'is_active' => 'boolean',
    ];

    /**
     * مبيعات نقطة البيع المرتبطة بهذه الخزينة.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(PosSale::class);
    }

    /**
     * المصروفات المرتبطة بهذه الخزينة.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * جميع حركات الخزينة.
     */
    public function movements(): HasMany
    {
        return $this->hasMany(CashMovement::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialAccount extends Model
{
    protected $fillable = [
        'name',
        'code',
        'type',
        'opening_balance',
        'current_balance',
        'currency',
        'is_active',
        'is_system',
        'notes',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:3',
        'current_balance' => 'decimal:3',
        'is_active' => 'boolean',
        'is_system' => 'boolean',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(FinanceTransaction::class);
    }
}

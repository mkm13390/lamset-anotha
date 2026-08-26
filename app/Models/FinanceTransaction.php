<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FinanceTransaction extends Model
{
    protected $fillable = [
        'transaction_number',
        'financial_account_id',
        'created_by',
        'transaction_type',
        'amount',
        'transaction_date',
        'category',
        'reference',
        'description',
        'source_type',
        'source_id',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:3',
        'transaction_date' => 'date',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(
            FinancialAccount::class,
            'financial_account_id'
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function isInflow(): bool
    {
        return in_array($this->transaction_type, [
            'income',
            'advance_repayment',
            'transfer_in',
            'adjustment_in',
        ], true);
    }

    public function isOutflow(): bool
    {
        return in_array($this->transaction_type, [
            'expense',
            'salary',
            'advance',
            'transfer_out',
            'adjustment_out',
        ], true);
    }
}

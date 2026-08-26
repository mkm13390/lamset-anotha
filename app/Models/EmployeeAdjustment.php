<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAdjustment extends Model
{
    protected $fillable = [
        'employee_profile_id',
        'created_by',
        'type',
        'amount',
        'effective_date',
        'is_recurring',
        'is_processed',
        'reference',
        'description',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:3',
        'effective_date' => 'date',
        'is_recurring' => 'boolean',
        'is_processed' => 'boolean',
    ];

    public function employeeProfile(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isCredit(): bool
    {
        return in_array($this->type, [
            'bonus',
            'commission',
            'overtime',
            'allowance',
            'other_credit',
        ], true);
    }

    public function isDebit(): bool
    {
        return in_array($this->type, [
            'deduction',
            'other_debit',
        ], true);
    }
}

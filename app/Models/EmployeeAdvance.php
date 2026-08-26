<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAdvance extends Model
{
    protected $fillable = [
        'employee_profile_id',
        'created_by',
        'amount',
        'deducted_amount',
        'balance_amount',
        'status',
        'advance_date',
        'first_deduction_date',
        'reference',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:3',
        'deducted_amount' => 'decimal:3',
        'balance_amount' => 'decimal:3',
        'advance_date' => 'date',
        'first_deduction_date' => 'date',
    ];

    public function employeeProfile(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getRemainingAttribute(): float
    {
        return round((float) $this->balance_amount, 3);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeProfile extends Model
{
    protected $fillable = [
        'user_id',
        'employee_code',
        'job_title',
        'hire_date',
        'basic_salary',
        'fixed_allowance',
        'salary_type',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'basic_salary' => 'decimal:3',
        'fixed_allowance' => 'decimal:3',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function advances(): HasMany
    {
        return $this->hasMany(EmployeeAdvance::class);
    }

    public function adjustments(): HasMany
    {
        return $this->hasMany(EmployeeAdjustment::class);
    }

    public function payrollEntries(): HasMany
    {
        return $this->hasMany(PayrollEntry::class);
    }

    public function getCurrentAdvanceBalanceAttribute(): float
    {
        return round(
            (float) $this->advances()
                ->where('status', 'active')
                ->sum('balance_amount'),
            3
        );
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollEntry extends Model
{
    protected $fillable = [
        'payroll_period_id',
        'employee_profile_id',
        'basic_salary',
        'allowances',
        'bonuses',
        'commissions',
        'overtime',
        'deductions',
        'advance_deduction',
        'gross_salary',
        'net_salary',
        'status',
        'paid_at',
        'payment_reference',
        'notes',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:3',
        'allowances' => 'decimal:3',
        'bonuses' => 'decimal:3',
        'commissions' => 'decimal:3',
        'overtime' => 'decimal:3',
        'deductions' => 'decimal:3',
        'advance_deduction' => 'decimal:3',
        'gross_salary' => 'decimal:3',
        'net_salary' => 'decimal:3',
        'paid_at' => 'datetime',
    ];

    public function payrollPeriod(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function employeeProfile(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAdjustment;
use App\Models\EmployeeAdvance;
use App\Models\EmployeeProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeFinanceController extends Controller
{
    public function index()
    {
        $employees = EmployeeProfile::query()
            ->with('user')
            ->withSum([
                'advances as active_advance_balance' => function ($query) {
                    $query->where('status', 'active');
                }
            ], 'balance_amount')
            ->orderBy('employee_code')
            ->paginate(30);

        $users = User::query()
            ->whereIn('role', ['staff', 'admin'])
            ->whereDoesntHave('employeeProfile')
            ->orderBy('name')
            ->get();

        return view('admin.finance.employees.index', compact(
            'employees',
            'users'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id', 'unique:employee_profiles,user_id'],
            'employee_code' => ['required', 'string', 'max:80', 'unique:employee_profiles,employee_code'],
            'job_title' => ['nullable', 'string', 'max:150'],
            'hire_date' => ['nullable', 'date'],
            'basic_salary' => ['required', 'numeric', 'min:0'],
            'fixed_allowance' => ['nullable', 'numeric', 'min:0'],
            'salary_type' => ['required', 'in:monthly,daily,hourly'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        EmployeeProfile::create([
            ...$validated,
            'fixed_allowance' => $validated['fixed_allowance'] ?? 0,
            'is_active' => true,
        ]);

        return back()->with('success', 'تم إنشاء الملف المالي للموظف.');
    }

    public function show(EmployeeProfile $employee)
    {
        $employee->load([
            'user',
            'advances' => fn ($q) => $q->latest('advance_date'),
            'adjustments' => fn ($q) => $q->latest('effective_date'),
            'payrollEntries.payrollPeriod',
        ]);

        return view('admin.finance.employees.show', compact('employee'));
    }

    public function update(Request $request, EmployeeProfile $employee)
    {
        $validated = $request->validate([
            'job_title' => ['nullable', 'string', 'max:150'],
            'hire_date' => ['nullable', 'date'],
            'basic_salary' => ['required', 'numeric', 'min:0'],
            'fixed_allowance' => ['nullable', 'numeric', 'min:0'],
            'salary_type' => ['required', 'in:monthly,daily,hourly'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $employee->update([
            ...$validated,
            'fixed_allowance' => $validated['fixed_allowance'] ?? 0,
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return back()->with('success', 'تم تحديث بيانات الموظف.');
    }

    public function addAdvance(Request $request, EmployeeProfile $employee)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'gt:0'],
            'advance_date' => ['required', 'date'],
            'first_deduction_date' => ['nullable', 'date'],
            'reference' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        EmployeeAdvance::create([
            'employee_profile_id' => $employee->id,
            'created_by' => auth()->id(),
            'amount' => $validated['amount'],
            'deducted_amount' => 0,
            'balance_amount' => $validated['amount'],
            'status' => 'active',
            'advance_date' => $validated['advance_date'],
            'first_deduction_date' => $validated['first_deduction_date'] ?? null,
            'reference' => $validated['reference'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'تم تسجيل السلفة.');
    }

    public function addAdjustment(Request $request, EmployeeProfile $employee)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:bonus,deduction,commission,overtime,allowance,other_credit,other_debit'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'effective_date' => ['required', 'date'],
            'is_recurring' => ['nullable', 'boolean'],
            'reference' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        EmployeeAdjustment::create([
            'employee_profile_id' => $employee->id,
            'created_by' => auth()->id(),
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'effective_date' => $validated['effective_date'],
            'is_recurring' => (bool) ($validated['is_recurring'] ?? false),
            'is_processed' => false,
            'reference' => $validated['reference'] ?? null,
            'description' => $validated['description'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'تم تسجيل الحركة على الموظف.');
    }
}

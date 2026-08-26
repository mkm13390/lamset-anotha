<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashMovement;
use App\Models\CashRegister;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ExpenseController extends Controller
{
    /**
     * عرض المصروفات والخزائن المتاحة.
     */
    public function index()
    {
        $expenses = Expense::query()
            ->with([
                'cashRegister',
                'user',
            ])
            ->latest('expense_date')
            ->latest('id')
            ->get();

        $registers = CashRegister::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $summary = [
            'count' => $expenses->count(),
            'total' => round((float) $expenses->sum('amount'), 3),
            'today' => round(
                (float) $expenses
                    ->where('expense_date', now()->toDateString())
                    ->sum('amount'),
                3
            ),
            'month' => round(
                (float) $expenses
                    ->filter(function ($expense) {
                        return optional($expense->expense_date)
                            ?->format('Y-m') === now()->format('Y-m');
                    })
                    ->sum('amount'),
                3
            ),
        ];

        return view('admin.expenses.index', compact(
            'expenses',
            'registers',
            'summary'
        ));
    }

    /**
     * حفظ مصروف جديد وخصمه من الخزينة إن تم اختيارها.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cash_register_id' => [
                'nullable',
                'integer',
                'exists:cash_registers,id',
            ],
            'category' => [
                'required',
                'string',
                'max:120',
            ],
            'title' => [
                'required',
                'string',
                'max:180',
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0.001',
            ],
            'expense_date' => [
                'required',
                'date',
            ],
            'payment_method' => [
                'required',
                'string',
                'max:50',
            ],
            'reference' => [
                'nullable',
                'string',
                'max:120',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        DB::transaction(function () use ($validated) {

            $amount = round(
                (float) $validated['amount'],
                3
            );

            $register = null;

            if (!empty($validated['cash_register_id'])) {
                $register = CashRegister::query()
                    ->lockForUpdate()
                    ->findOrFail($validated['cash_register_id']);

                if (!$register->is_active) {
                    throw ValidationException::withMessages([
                        'cash_register_id' => 'الخزينة المختارة غير مفعلة.',
                    ]);
                }

                $currentBalance = (float) $register->current_balance;

                if ($amount > $currentBalance) {
                    throw ValidationException::withMessages([
                        'amount' => 'قيمة المصروف أكبر من رصيد الخزينة الحالية.',
                    ]);
                }
            }

            $expense = Expense::create([
                'cash_register_id' => $register?->id,
                'user_id' => auth()->id(),
                'category' => $validated['category'],
                'title' => $validated['title'],
                'amount' => $amount,
                'expense_date' => $validated['expense_date'],
                'payment_method' => $validated['payment_method'],
                'reference' => $validated['reference'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            if ($register) {
                $newBalance = round(
                    (float) $register->current_balance - $amount,
                    3
                );

                $register->update([
                    'current_balance' => $newBalance,
                ]);

                CashMovement::create([
                    'cash_register_id' => $register->id,
                    'user_id' => auth()->id(),
                    'type' => 'expense',
                    'amount' => $amount,
                    'balance_after' => $newBalance,
                    'reference' => $expense->reference
                        ?: 'EXP-' . $expense->id,
                    'description' => $expense->title,
                ]);
            }
        });

        return redirect()
            ->route('admin.expenses.index')
            ->with('success', 'تم تسجيل المصروف بنجاح.');
    }

    /**
     * حذف المصروف.
     *
     * إذا كان المصروف مرتبطًا بخزينة،
     * يتم إعادة المبلغ إلى الخزينة وتسجيل حركة تصحيح.
     */
    public function destroy(Expense $expense)
    {
        DB::transaction(function () use ($expense) {

            if ($expense->cash_register_id) {
                $register = CashRegister::query()
                    ->lockForUpdate()
                    ->find($expense->cash_register_id);

                if ($register) {
                    $amount = (float) $expense->amount;

                    $newBalance = round(
                        (float) $register->current_balance + $amount,
                        3
                    );

                    $register->update([
                        'current_balance' => $newBalance,
                    ]);

                    CashMovement::create([
                        'cash_register_id' => $register->id,
                        'user_id' => auth()->id(),
                        'type' => 'adjustment',
                        'amount' => $amount,
                        'balance_after' => $newBalance,
                        'reference' => 'EXP-DELETE-' . $expense->id,
                        'description' => 'إلغاء مصروف وإعادة المبلغ للخزينة',
                    ]);
                }
            }

            $expense->delete();
        });

        return redirect()
            ->route('admin.expenses.index')
            ->with('success', 'تم حذف المصروف وإجراء التصحيح المالي بنجاح.');
    }
}

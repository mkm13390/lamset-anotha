<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashMovement;
use App\Models\CashRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashRegisterController extends Controller
{
    /**
     * عرض الخزائن وحركاتها الأخيرة.
     */
    public function index()
    {
        $registers = CashRegister::query()
            ->latest()
            ->get();

        $movements = CashMovement::query()
            ->with('cashRegister')
            ->latest()
            ->limit(30)
            ->get();

        return view('admin.cash-registers.index', compact(
            'registers',
            'movements'
        ));
    }

    /**
     * إنشاء خزينة جديدة.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:120',
            ],
            'opening_balance' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $openingBalance = round(
            (float) ($validated['opening_balance'] ?? 0),
            3
        );

        DB::transaction(function () use (
            $validated,
            $openingBalance,
            $request
        ) {
            $register = CashRegister::create([
                'name' => $validated['name'],
                'opening_balance' => $openingBalance,
                'current_balance' => $openingBalance,
                'is_active' => $request->boolean('is_active'),
            ]);

            if ($openingBalance > 0) {
                CashMovement::create([
                    'cash_register_id' => $register->id,
                    'user_id' => auth()->id(),
                    'type' => 'opening',
                    'amount' => $openingBalance,
                    'balance_after' => $openingBalance,
                    'reference' => 'OPENING-' . $register->id,
                    'description' => 'الرصيد الافتتاحي للخزينة',
                ]);
            }
        });

        return redirect()
            ->route('admin.cash-registers.index')
            ->with('success', 'تم إنشاء الخزينة بنجاح.');
    }

    /**
     * تفعيل أو تعطيل الخزينة.
     */
    public function toggle(CashRegister $cashRegister)
    {
        $cashRegister->update([
            'is_active' => !$cashRegister->is_active,
        ]);

        return redirect()
            ->route('admin.cash-registers.index')
            ->with('success', 'تم تحديث حالة الخزينة.');
    }

    /**
     * إضافة إيداع أو سحب أو تعديل.
     */
    public function movement(Request $request, CashRegister $cashRegister)
    {
        $validated = $request->validate([
            'type' => [
                'required',
                'in:deposit,withdrawal,adjustment',
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0.001',
            ],
            'description' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        DB::transaction(function () use (
            $validated,
            $cashRegister
        ) {
            $register = CashRegister::query()
                ->lockForUpdate()
                ->findOrFail($cashRegister->id);

            $amount = round(
                (float) $validated['amount'],
                3
            );

            $currentBalance = (float) $register->current_balance;

            if ($validated['type'] === 'withdrawal') {
                if ($amount > $currentBalance) {
                    abort(422, 'مبلغ السحب أكبر من رصيد الخزينة.');
                }

                $newBalance = $currentBalance - $amount;
            } else {
                $newBalance = $currentBalance + $amount;
            }

            $newBalance = round($newBalance, 3);

            $register->update([
                'current_balance' => $newBalance,
            ]);

            CashMovement::create([
                'cash_register_id' => $register->id,
                'user_id' => auth()->id(),
                'type' => $validated['type'],
                'amount' => $amount,
                'balance_after' => $newBalance,
                'reference' => strtoupper($validated['type'])
                    . '-'
                    . now()->format('YmdHis'),
                'description' => $validated['description'] ?? null,
            ]);
        });

        return redirect()
            ->route('admin.cash-registers.index')
            ->with('success', 'تم تسجيل حركة الخزينة بنجاح.');
    }
}

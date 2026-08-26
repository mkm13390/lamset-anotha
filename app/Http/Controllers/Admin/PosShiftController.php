<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashMovement;
use App\Models\CashRegister;
use App\Models\PosShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PosShiftController extends Controller
{
    public function index()
    {
        $registers = CashRegister::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $openShift = PosShift::query()
            ->with('cashRegister')
            ->where('user_id', auth()->id())
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();

        $recentShifts = PosShift::query()
            ->with([
                'cashRegister',
                'user',
            ])
            ->latest('opened_at')
            ->limit(20)
            ->get();

        return view(
            'admin.pos.shifts.index',
            compact(
                'registers',
                'openShift',
                'recentShifts'
            )
        );
    }

    public function open(Request $request)
    {
        $validated = $request->validate([
            'cash_register_id' => [
                'required',
                'integer',
                'exists:cash_registers,id',
            ],
            'opening_cash' => [
                'required',
                'numeric',
                'min:0',
            ],
            'opening_notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $existingShift = PosShift::query()
            ->where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        if ($existingShift) {
            return back()->withErrors([
                'shift' => 'لديك وردية مفتوحة بالفعل. أغلقها قبل فتح وردية جديدة.',
            ]);
        }

        DB::transaction(function () use ($validated) {
            $register = CashRegister::query()
                ->lockForUpdate()
                ->findOrFail($validated['cash_register_id']);

            if (!$register->is_active) {
                throw ValidationException::withMessages([
                    'cash_register_id' => 'الخزينة المختارة غير مفعلة.',
                ]);
            }

            $openingCash = round(
                (float) $validated['opening_cash'],
                3
            );

            $shift = PosShift::create([
                'cash_register_id' => $register->id,
                'user_id' => auth()->id(),
                'status' => 'open',
                'opening_cash' => $openingCash,
                'cash_sales' => 0,
                'cash_refunds' => 0,
                'cash_in' => 0,
                'cash_out' => 0,
                'expected_cash' => $openingCash,
                'opened_at' => now(),
                'opening_notes' => $validated['opening_notes'] ?? null,
            ]);

            $register->update([
                'current_balance' => $openingCash,
            ]);

            CashMovement::create([
                'cash_register_id' => $register->id,
                'user_id' => auth()->id(),
                'type' => 'shift_open',
                'amount' => $openingCash,
                'balance_after' => $openingCash,
                'reference' => 'SHIFT-' . $shift->id . '-OPEN',
                'description' => 'فتح وردية كاشير',
            ]);
        });

        return redirect()
            ->route('admin.pos.shifts.index')
            ->with('success', 'تم فتح الوردية بنجاح.');
    }

    public function close(Request $request, PosShift $shift)
    {
        if (
            $shift->user_id !== auth()->id()
            && !auth()->user()?->is_admin
        ) {
            abort(403);
        }

        if (!$shift->isOpen()) {
            return back()->withErrors([
                'shift' => 'هذه الوردية مغلقة بالفعل.',
            ]);
        }

        $validated = $request->validate([
            'closing_cash' => [
                'required',
                'numeric',
                'min:0',
            ],
            'closing_notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        DB::transaction(function () use ($shift, $validated) {
            $lockedShift = PosShift::query()
                ->lockForUpdate()
                ->findOrFail($shift->id);

            if (!$lockedShift->isOpen()) {
                throw ValidationException::withMessages([
                    'shift' => 'هذه الوردية مغلقة بالفعل.',
                ]);
            }

            $lockedShift->recalculateExpectedCash();
            $lockedShift->refresh();

            $closingCash = round(
                (float) $validated['closing_cash'],
                3
            );

            $difference = round(
                $closingCash
                - (float) $lockedShift->expected_cash,
                3
            );

            $lockedShift->update([
                'status' => 'closed',
                'closing_cash' => $closingCash,
                'difference_amount' => $difference,
                'closed_at' => now(),
                'closing_notes' => $validated['closing_notes'] ?? null,
            ]);

            $register = CashRegister::query()
                ->lockForUpdate()
                ->find($lockedShift->cash_register_id);

            if ($register) {
                $register->update([
                    'current_balance' => $closingCash,
                ]);

                CashMovement::create([
                    'cash_register_id' => $register->id,
                    'user_id' => auth()->id(),
                    'type' => 'shift_close',
                    'amount' => $closingCash,
                    'balance_after' => $closingCash,
                    'reference' => 'SHIFT-' . $lockedShift->id . '-CLOSE',
                    'description' => 'إغلاق وردية كاشير',
                ]);
            }
        });

        return redirect()
            ->route('admin.pos.shifts.index')
            ->with('success', 'تم إغلاق الوردية بنجاح.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\SupplierTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SupplierPaymentController extends Controller
{
    public function store(
        Request $request,
        Supplier $supplier
    ): RedirectResponse {
        $validated = $request->validate([
            'purchase_order_id' => [
                'nullable',
                'integer',
                'exists:purchase_orders,id',
            ],
            'payment_date' => [
                'required',
                'date',
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0.001',
            ],
            'payment_method' => [
                'required',
                'in:cash,card,bank_transfer,cheque,other',
            ],
            'reference' => [
                'nullable',
                'string',
                'max:150',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        DB::transaction(function () use (
            $validated,
            $supplier
        ) {
            $supplier = Supplier::query()
                ->lockForUpdate()
                ->findOrFail($supplier->id);

            $amount = round(
                (float) $validated['amount'],
                3
            );

            $purchase = null;

            if (! empty($validated['purchase_order_id'])) {
                $purchase = PurchaseOrder::query()
                    ->lockForUpdate()
                    ->findOrFail(
                        $validated['purchase_order_id']
                    );

                if ((int) $purchase->supplier_id !== (int) $supplier->id) {
                    throw ValidationException::withMessages([
                        'purchase_order_id' =>
                            'أمر الشراء المحدد لا يتبع هذا المورد.',
                    ]);
                }

                $remaining = round(
                    (float) $purchase->balance_due,
                    3
                );

                if ($amount > $remaining) {
                    throw ValidationException::withMessages([
                        'amount' =>
                            'المبلغ المدفوع أكبر من الرصيد المتبقي على أمر الشراء.',
                    ]);
                }
            }

            if (
                $amount > round(
                    (float) $supplier->current_balance,
                    3
                )
            ) {
                throw ValidationException::withMessages([
                    'amount' =>
                        'المبلغ المدفوع أكبر من الرصيد المستحق للمورد.',
                ]);
            }

            $payment = SupplierPayment::create([
                'supplier_id' => $supplier->id,
                'purchase_order_id' => $purchase?->id,
                'user_id' => auth()->id(),
                'payment_number' => $this->nextPaymentNumber(),
                'payment_date' => $validated['payment_date'],
                'amount' => $amount,
                'payment_method' => $validated['payment_method'],
                'reference' => $validated['reference'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            $newSupplierBalance = round(
                max(
                    (float) $supplier->current_balance
                    - $amount,
                    0
                ),
                3
            );

            $supplier->update([
                'current_balance' => $newSupplierBalance,
            ]);

            if ($purchase) {
                $newPaidAmount = round(
                    (float) $purchase->paid_amount
                    + $amount,
                    3
                );

                $newBalanceDue = round(
                    max(
                        (float) $purchase->total
                        - $newPaidAmount,
                        0
                    ),
                    3
                );

                $paymentStatus = 'unpaid';

                if (
                    $newPaidAmount > 0
                    && $newBalanceDue > 0
                ) {
                    $paymentStatus = 'partial';
                } elseif ($newBalanceDue <= 0) {
                    $paymentStatus = 'paid';
                }

                $purchase->update([
                    'paid_amount' => $newPaidAmount,
                    'balance_due' => $newBalanceDue,
                    'payment_status' => $paymentStatus,
                ]);
            }

            SupplierTransaction::create([
                'supplier_id' => $supplier->id,
                'purchase_order_id' => $purchase?->id,
                'supplier_payment_id' => $payment->id,
                'user_id' => auth()->id(),
                'type' => 'payment',
                'transaction_date' => $validated['payment_date'],
                'debit' => 0,
                'credit' => $amount,
                'balance_after' => $newSupplierBalance,
                'reference' => $payment->payment_number,
                'description' => $purchase
                    ? 'دفعة للمورد مقابل أمر الشراء '
                        . $purchase->purchase_number
                    : 'دفعة عامة للمورد',
            ]);
        });

        return back()->with(
            'success',
            'تم تسجيل دفعة المورد وتحديث الرصيد بنجاح.'
        );
    }

    private function nextPaymentNumber(): string
    {
        do {
            $number = 'SP-'
                . now()->format('YmdHis')
                . '-'
                . random_int(1000, 9999);
        } while (
            SupplierPayment::where(
                'payment_number',
                $number
            )->exists()
        );

        return $number;
    }
}

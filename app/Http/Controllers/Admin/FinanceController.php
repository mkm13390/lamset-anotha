<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceTransaction;
use App\Models\FinancialAccount;
use App\Services\FinanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function __construct(
        private readonly FinanceService $financeService
    ) {
    }

    public function index(Request $request)
    {
        $accounts = FinancialAccount::query()
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $transactions = FinanceTransaction::query()
            ->with(['account', 'creator'])
            ->when($request->filled('account_id'), function ($query) use ($request) {
                $query->where('financial_account_id', $request->integer('account_id'));
            })
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('transaction_type', $request->string('type'));
            })
            ->when($request->filled('from'), function ($query) use ($request) {
                $query->whereDate('transaction_date', '>=', $request->date('from'));
            })
            ->when($request->filled('to'), function ($query) use ($request) {
                $query->whereDate('transaction_date', '<=', $request->date('to'));
            })
            ->latest('transaction_date')
            ->latest('id')
            ->paginate(50)
            ->withQueryString();

        $stats = [
            'total_balance' => round((float) $accounts->sum('current_balance'), 3),
            'income_month' => round((float) FinanceTransaction::query()
                ->where('transaction_type', 'income')
                ->whereYear('transaction_date', now()->year)
                ->whereMonth('transaction_date', now()->month)
                ->sum('amount'), 3),
            'expense_month' => round((float) FinanceTransaction::query()
                ->whereIn('transaction_type', ['expense', 'salary', 'advance', 'adjustment_out'])
                ->whereYear('transaction_date', now()->year)
                ->whereMonth('transaction_date', now()->month)
                ->sum('amount'), 3),
        ];

        return view('admin.finance.index', compact(
            'accounts',
            'transactions',
            'stats'
        ));
    }

    public function storeAccount(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:80', 'unique:financial_accounts,code'],
            'type' => ['required', 'in:cash,bank,card_clearing,income,expense,liability,asset,other'],
            'opening_balance' => ['nullable', 'numeric'],
            'currency' => ['nullable', 'string', 'size:3'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $opening = round((float) ($validated['opening_balance'] ?? 0), 3);

        FinancialAccount::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'type' => $validated['type'],
            'opening_balance' => $opening,
            'current_balance' => $opening,
            'currency' => strtoupper($validated['currency'] ?? 'OMR'),
            'is_active' => true,
            'is_system' => false,
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'تم إنشاء الحساب المالي.');
    }

    public function storeTransaction(Request $request)
    {
        $validated = $request->validate([
            'financial_account_id' => ['required', 'integer', 'exists:financial_accounts,id'],
            'transaction_type' => ['required', 'in:income,expense,advance_repayment,transfer_in,transfer_out,adjustment_in,adjustment_out'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'transaction_date' => ['required', 'date'],
            'category' => ['nullable', 'string', 'max:150'],
            'reference' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->financeService->createTransaction($validated);

        return back()->with('success', 'تم تسجيل الحركة المالية.');
    }
}

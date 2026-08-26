<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialAccount;
use App\Models\PayrollPeriod;
use App\Services\PayrollService;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function __construct(
        private readonly PayrollService $payrollService
    ) {
    }

    public function index()
    {
        $periods = PayrollPeriod::query()
            ->withCount('entries')
            ->latest('start_date')
            ->paginate(24);

        return view('admin.finance.payroll.index', compact('periods'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'period_name' => ['required', 'string', 'max:120'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'payment_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $period = PayrollPeriod::create([
            ...$validated,
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.finance.payroll.show', $period)
            ->with('success', 'تم إنشاء فترة الرواتب.');
    }

    public function show(PayrollPeriod $period)
    {
        $period->load([
            'entries.employeeProfile.user',
            'creator',
            'approver',
        ]);

        $accounts = FinancialAccount::query()
            ->where('is_active', true)
            ->whereIn('type', ['cash', 'bank'])
            ->orderBy('name')
            ->get();

        return view('admin.finance.payroll.show', compact(
            'period',
            'accounts'
        ));
    }

    public function calculate(PayrollPeriod $period)
    {
        $this->payrollService->calculatePeriod($period);

        return back()->with('success', 'تم احتساب مسير الرواتب.');
    }

    public function approve(PayrollPeriod $period)
    {
        $this->payrollService->approvePeriod($period);

        return back()->with('success', 'تم اعتماد مسير الرواتب.');
    }

    public function pay(Request $request, PayrollPeriod $period)
    {
        $validated = $request->validate([
            'financial_account_id' => [
                'required',
                'integer',
                'exists:financial_accounts,id',
            ],
            'reference' => ['nullable', 'string', 'max:150'],
        ]);

        $account = FinancialAccount::findOrFail(
            $validated['financial_account_id']
        );

        $this->payrollService->payPeriod(
            $period,
            $account,
            $validated['reference'] ?? null
        );

        return back()->with('success', 'تم صرف مسير الرواتب وتسجيل الحركة المالية.');
    }
}

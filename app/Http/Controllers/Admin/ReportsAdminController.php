<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SavedReport;
use App\Models\ScheduledReport;
use Illuminate\Http\Request;

class ReportsAdminController extends Controller
{
    public function index()
    {
        $savedReports = SavedReport::query()
            ->with(['user', 'schedules'])
            ->latest()
            ->paginate(30);

        return view('admin.reports.manage', compact('savedReports'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'report_key' => ['required', 'string', 'max:160'],
            'visibility' => ['required', 'in:private,role,public'],
            'role_scope' => ['nullable', 'string', 'max:80'],
        ]);

        SavedReport::create([
            'user_id' => auth()->id(),
            ...$validated,
            'filters' => null,
            'columns' => null,
            'is_favorite' => false,
        ]);

        return back()->with('success', 'تم حفظ التقرير.');
    }

    public function schedule(Request $request, SavedReport $savedReport)
    {
        $validated = $request->validate([
            'frequency' => ['required', 'string', 'max:80'],
            'delivery_channel' => ['required', 'in:email,system'],
            'recipients' => ['nullable', 'array'],
            'recipients.*' => ['nullable', 'string', 'max:190'],
            'timezone' => ['nullable', 'string', 'max:80'],
            'next_run_at' => ['nullable', 'date'],
        ]);

        ScheduledReport::create([
            'saved_report_id' => $savedReport->id,
            'created_by' => auth()->id(),
            'frequency' => $validated['frequency'],
            'delivery_channel' => $validated['delivery_channel'],
            'recipients' => $validated['recipients'] ?? null,
            'timezone' => $validated['timezone'] ?? 'Asia/Muscat',
            'is_active' => true,
            'next_run_at' => $validated['next_run_at'] ?? null,
        ]);

        return back()->with('success', 'تمت جدولة التقرير.');
    }
}

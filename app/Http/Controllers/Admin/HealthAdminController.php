<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemHealthCheck;
use App\Models\SystemIncident;
use App\Services\SystemHealthService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HealthAdminController extends Controller
{
    public function __construct(
        private readonly SystemHealthService $health
    ) {
    }

    public function index()
    {
        $latestChecks = SystemHealthCheck::query()
            ->orderByDesc('checked_at')
            ->get()
            ->unique('check_key')
            ->values();

        $incidents = SystemIncident::query()
            ->with(['assignee', 'resolver'])
            ->latest('detected_at')
            ->paginate(30);

        return view('admin.health.index', compact(
            'latestChecks',
            'incidents'
        ));
    }

    public function runChecks()
    {
        $this->health->runAll();

        return back()->with('success', 'تم تنفيذ فحوصات صحة النظام.');
    }

    public function storeIncident(Request $request)
    {
        $validated = $request->validate([
            'severity' => ['required', 'in:info,warning,high,critical'],
            'title' => ['required', 'string', 'max:220'],
            'description' => ['nullable', 'string'],
            'source' => ['nullable', 'string', 'max:160'],
        ]);

        do {
            $number = 'INC-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
        } while (
            SystemIncident::where('incident_number', $number)->exists()
        );

        SystemIncident::create([
            'incident_number' => $number,
            ...$validated,
            'status' => 'open',
            'detected_at' => now(),
        ]);

        return back()->with('success', 'تم تسجيل الحادثة.');
    }

    public function resolveIncident(SystemIncident $incident)
    {
        $incident->update([
            'status' => 'resolved',
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        return back()->with('success', 'تم إغلاق الحادثة.');
    }
}

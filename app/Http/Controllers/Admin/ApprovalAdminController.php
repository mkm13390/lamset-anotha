<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApprovalRequest;
use App\Services\ApprovalService;
use Illuminate\Http\Request;

class ApprovalAdminController extends Controller
{
    public function __construct(
        private readonly ApprovalService $approvals
    ) {
    }

    public function index(Request $request)
    {
        $requests = ApprovalRequest::query()
            ->with(['requester', 'reviewer'])
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where(
                    'status',
                    $request->string('status')->toString()
                );
            })
            ->latest('requested_at')
            ->paginate(40)
            ->withQueryString();

        return view('admin.approvals.index', compact('requests'));
    }

    public function review(
        Request $request,
        ApprovalRequest $approvalRequest
    ) {
        $validated = $request->validate([
            'decision' => ['required', 'in:approved,rejected'],
            'review_notes' => ['nullable', 'string', 'max:3000'],
        ]);

        $this->approvals->review(
            $approvalRequest,
            $validated['decision'],
            $validated['review_notes'] ?? null
        );

        return back()->with('success', 'تمت معالجة طلب الموافقة.');
    }
}

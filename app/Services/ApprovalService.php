<?php

namespace App\Services;

use App\Models\ApprovalRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ApprovalService
{
    public function request(
        string $approvalType,
        ?Model $approvable = null,
        ?string $reason = null,
        ?string $notes = null,
        array $payload = []
    ): ApprovalRequest {
        do {
            $number = 'APR-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(5));
        } while (
            ApprovalRequest::where('request_number', $number)->exists()
        );

        return ApprovalRequest::create([
            'request_number' => $number,
            'approvable_type' => $approvable?->getMorphClass(),
            'approvable_id' => $approvable?->getKey(),
            'approval_type' => $approvalType,
            'requested_by' => auth()->id(),
            'status' => 'pending',
            'reason' => $reason,
            'request_notes' => $notes,
            'payload' => $payload ?: null,
            'requested_at' => now(),
        ]);
    }

    public function review(
        ApprovalRequest $approvalRequest,
        string $decision,
        ?string $notes = null
    ): ApprovalRequest {
        if (!in_array($decision, ['approved', 'rejected'], true)) {
            throw ValidationException::withMessages([
                'decision' => 'قرار الموافقة غير صالح.',
            ]);
        }

        return DB::transaction(function () use (
            $approvalRequest,
            $decision,
            $notes
        ) {
            $approvalRequest = ApprovalRequest::query()
                ->lockForUpdate()
                ->findOrFail($approvalRequest->id);

            if ($approvalRequest->status !== 'pending') {
                throw ValidationException::withMessages([
                    'approval' => 'تمت معالجة هذا الطلب مسبقًا.',
                ]);
            }

            $approvalRequest->update([
                'status' => $decision,
                'reviewed_by' => auth()->id(),
                'review_notes' => $notes,
                'reviewed_at' => now(),
            ]);

            return $approvalRequest->refresh();
        });
    }
}

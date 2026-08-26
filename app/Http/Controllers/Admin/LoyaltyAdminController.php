<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerLoyaltyProfile;
use App\Models\User;
use App\Services\LoyaltyMarketingService;
use Illuminate\Http\Request;

class LoyaltyAdminController extends Controller
{
    public function __construct(
        private readonly LoyaltyMarketingService $loyaltyService
    ) {
    }

    public function index(Request $request)
    {
        $profiles = CustomerLoyaltyProfile::query()
            ->with(['user', 'tier'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->string('q')->toString();

                $query->whereHas('user', function ($sub) use ($q) {
                    $sub->where('name', 'like', '%' . $q . '%')
                        ->orWhere('phone', 'like', '%' . $q . '%')
                        ->orWhere('email', 'like', '%' . $q . '%');
                });
            })
            ->latest('lifetime_spend')
            ->paginate(40)
            ->withQueryString();

        return view('admin.marketing.loyalty', compact('profiles'));
    }

    public function ensureProfile(User $user)
    {
        $this->loyaltyService->profileFor($user);

        return back()->with('success', 'تم إنشاء/تحديث ملف الولاء للعميل.');
    }

    public function addPoints(Request $request, User $user)
    {
        $validated = $request->validate([
            'points' => ['required', 'integer', 'min:1'],
        ]);

        $this->loyaltyService->awardPoints(
            $user,
            (int) $validated['points']
        );

        return back()->with('success', 'تمت إضافة النقاط.');
    }

    public function addStoreCredit(Request $request, User $user)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'gt:0'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $this->loyaltyService->addStoreCredit(
            $user,
            (float) $validated['amount'],
            'adjustment',
            null,
            $validated['description'] ?? 'إضافة رصيد متجر من الإدارة'
        );

        return back()->with('success', 'تمت إضافة رصيد المتجر.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GiftCard;
use App\Models\GiftCardTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GiftCardController extends Controller
{
    public function index()
    {
        $cards = GiftCard::query()
            ->with(['purchaser', 'assignedUser'])
            ->latest()
            ->paginate(40);

        return view('admin.marketing.gift-cards', compact('cards'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'initial_balance' => ['required', 'numeric', 'gt:0'],
            'assigned_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'expires_at' => ['nullable', 'date'],
            'recipient_name' => ['nullable', 'string', 'max:150'],
            'recipient_phone' => ['nullable', 'string', 'max:50'],
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        do {
            $code = 'GIFT-' . strtoupper(Str::random(10));
        } while (GiftCard::where('code', $code)->exists());

        $card = GiftCard::create([
            'code' => $code,
            'initial_balance' => $validated['initial_balance'],
            'current_balance' => $validated['initial_balance'],
            'assigned_user_id' => $validated['assigned_user_id'] ?? null,
            'status' => 'active',
            'expires_at' => $validated['expires_at'] ?? null,
            'recipient_name' => $validated['recipient_name'] ?? null,
            'recipient_phone' => $validated['recipient_phone'] ?? null,
            'message' => $validated['message'] ?? null,
        ]);

        GiftCardTransaction::create([
            'gift_card_id' => $card->id,
            'type' => 'issue',
            'amount' => $validated['initial_balance'],
            'balance_after' => $validated['initial_balance'],
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'تم إنشاء بطاقة الهدية.');
    }
}

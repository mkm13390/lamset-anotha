<?php

namespace App\Http\Controllers;

use App\Models\CustomerAddress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerAddressController extends Controller
{
    public function index(Request $request): View
    {
        $addresses = CustomerAddress::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        return view('account.addresses.index', compact('addresses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateAddress($request);

        $userId = $request->user()->id;

        if (! CustomerAddress::where('user_id', $userId)->exists()) {
            $data['is_default'] = true;
        }

        if (! empty($data['is_default'])) {
            CustomerAddress::where('user_id', $userId)
                ->update(['is_default' => false]);
        }

        $data['user_id'] = $userId;
        $data['is_active'] = true;

        CustomerAddress::create($data);

        return back()->with('success', 'تم إضافة العنوان بنجاح.');
    }

    public function update(Request $request, CustomerAddress $address): RedirectResponse
    {
        $this->authorizeOwner($request, $address);

        $data = $this->validateAddress($request);

        if (! empty($data['is_default'])) {
            CustomerAddress::where('user_id', $request->user()->id)
                ->whereKeyNot($address->id)
                ->update(['is_default' => false]);
        }

        $address->update($data);

        return back()->with('success', 'تم تحديث العنوان بنجاح.');
    }

    public function setDefault(Request $request, CustomerAddress $address): RedirectResponse
    {
        $this->authorizeOwner($request, $address);

        CustomerAddress::where('user_id', $request->user()->id)
            ->update(['is_default' => false]);

        $address->update([
            'is_default' => true,
            'is_active' => true,
        ]);

        return back()->with('success', 'تم تعيين العنوان كعنوان افتراضي.');
    }

    public function destroy(Request $request, CustomerAddress $address): RedirectResponse
    {
        $this->authorizeOwner($request, $address);

        $wasDefault = $address->is_default;

        $address->delete();

        if ($wasDefault) {
            $nextAddress = CustomerAddress::where('user_id', $request->user()->id)
                ->latest()
                ->first();

            if ($nextAddress) {
                $nextAddress->update(['is_default' => true]);
            }
        }

        return back()->with('success', 'تم حذف العنوان.');
    }

    private function validateAddress(Request $request): array
    {
        return $request->validate([
            'label' => ['nullable', 'string', 'max:50'],
            'full_name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'governorate' => ['required', 'string', 'max:100'],
            'wilayat' => ['required', 'string', 'max:100'],
            'area' => ['nullable', 'string', 'max:150'],
            'street' => ['nullable', 'string', 'max:150'],
            'building' => ['nullable', 'string', 'max:100'],
            'house_number' => ['nullable', 'string', 'max:100'],
            'address_details' => ['nullable', 'string', 'max:1000'],
            'postal_code' => ['nullable', 'string', 'max:30'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'is_default' => ['nullable', 'boolean'],
        ]);
    }

    private function authorizeOwner(Request $request, CustomerAddress $address): void
    {
        abort_unless($address->user_id === $request->user()->id, 403);
    }
}

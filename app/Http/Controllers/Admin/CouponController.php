<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    /**
     * عرض جميع الكوبونات.
     */
    public function index()
    {
        $coupons = Coupon::query()
            ->latest()
            ->get();

        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * حفظ كوبون جديد.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                'unique:coupons,code',
            ],
            'name' => [
                'nullable',
                'string',
                'max:120',
            ],
            'discount_type' => [
                'required',
                Rule::in(['percentage', 'fixed']),
            ],
            'discount_value' => [
                'required',
                'numeric',
                'min:0.001',
            ],
            'minimum_order_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'starts_at' => [
                'nullable',
                'date',
            ],
            'ends_at' => [
                'nullable',
                'date',
                'after_or_equal:starts_at',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        if (
            $validated['discount_type'] === 'percentage'
            && (float) $validated['discount_value'] > 100
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'discount_value' => 'قيمة الخصم بالنسبة المئوية لا يمكن أن تتجاوز 100%.',
                ]);
        }

        Coupon::create([
            'code' => strtoupper(trim($validated['code'])),
            'name' => $validated['name'] ?? null,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'minimum_order_amount' => $validated['minimum_order_amount'] ?? 0,
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'تم إنشاء الكوبون بنجاح.');
    }

    /**
     * تحديث كوبون موجود.
     */
    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('coupons', 'code')->ignore($coupon->id),
            ],
            'name' => [
                'nullable',
                'string',
                'max:120',
            ],
            'discount_type' => [
                'required',
                Rule::in(['percentage', 'fixed']),
            ],
            'discount_value' => [
                'required',
                'numeric',
                'min:0.001',
            ],
            'minimum_order_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'starts_at' => [
                'nullable',
                'date',
            ],
            'ends_at' => [
                'nullable',
                'date',
                'after_or_equal:starts_at',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        if (
            $validated['discount_type'] === 'percentage'
            && (float) $validated['discount_value'] > 100
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'discount_value' => 'قيمة الخصم بالنسبة المئوية لا يمكن أن تتجاوز 100%.',
                ]);
        }

        $coupon->update([
            'code' => strtoupper(trim($validated['code'])),
            'name' => $validated['name'] ?? null,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'minimum_order_amount' => $validated['minimum_order_amount'] ?? 0,
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'تم تحديث الكوبون بنجاح.');
    }

    /**
     * تفعيل أو تعطيل الكوبون.
     */
    public function toggle(Coupon $coupon)
    {
        $coupon->update([
            'is_active' => !$coupon->is_active,
        ]);

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'تم تحديث حالة الكوبون.');
    }

    /**
     * حذف الكوبون.
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'تم حذف الكوبون.');
    }
}

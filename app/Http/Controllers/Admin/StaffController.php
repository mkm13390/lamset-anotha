<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StaffController extends Controller
{
    /**
     * عرض الموظفين والمديرين.
     */
    public function index()
    {
        $staff = User::query()
            ->whereIn('role', ['staff', 'admin'])
            ->latest()
            ->get();

        return view('admin.staff.index', compact('staff'));
    }

    /**
     * إنشاء موظف أو مدير جديد.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:120',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'phone' => [
                'required',
                'string',
                'max:30',
                'unique:users,phone',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],
            'role' => [
                'required',
                Rule::in(['staff', 'admin']),
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $user = new User();

        $user->name = $validated['name'];
        $user->email = strtolower($validated['email']);
        $user->phone = $validated['phone'];
        $user->password = Hash::make($validated['password']);
        $user->role = $validated['role'];
        $user->is_active = $request->boolean('is_active');

        $user->save();

        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'تم إنشاء الحساب بنجاح.');
    }

    /**
     * تحديث دور المستخدم وحالة الحساب.
     */
    public function update(Request $request, User $user)
    {
        if (!in_array($user->role, ['staff', 'admin'], true)) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:120',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => [
                'required',
                'string',
                'max:30',
                Rule::unique('users', 'phone')->ignore($user->id),
            ],
            'role' => [
                'required',
                Rule::in(['staff', 'admin']),
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $user->name = $validated['name'];
        $user->email = strtolower($validated['email']);
        $user->phone = $validated['phone'];
        $user->role = $validated['role'];
        $user->is_active = $request->boolean('is_active');

        $user->save();

        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'تم تحديث الحساب بنجاح.');
    }

    /**
     * تغيير كلمة مرور موظف أو مدير.
     */
    public function updatePassword(Request $request, User $user)
    {
        if (!in_array($user->role, ['staff', 'admin'], true)) {
            abort(404);
        }

        $validated = $request->validate([
            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],
        ]);

        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'تم تغيير كلمة المرور بنجاح.');
    }

    /**
     * تفعيل أو تعطيل الحساب.
     */
    public function toggle(User $user)
    {
        if (!in_array($user->role, ['staff', 'admin'], true)) {
            abort(404);
        }

        if (auth()->id() === $user->id) {
            return back()->withErrors([
                'staff' => 'لا يمكنك تعطيل حسابك الحالي.',
            ]);
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'تم تحديث حالة الحساب.');
    }
}

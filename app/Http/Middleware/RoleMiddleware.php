<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * السماح بالدخول فقط للمستخدمين المفعّلين
     * والذين يملكون إحدى الصلاحيات المطلوبة.
     *
     * مثال:
     * ->middleware('role:admin')
     * ->middleware('role:admin,staff')
     */
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        $user = $request->user();

        if (!$user) {
            return redirect()
                ->route('login');
        }

        if (!$user->is_active) {
            abort(403, 'هذا الحساب غير مفعّل.');
        }

        if (!in_array($user->role, $roles, true)) {
            abort(403, 'ليس لديك صلاحية للوصول إلى هذه الصفحة.');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\FeatureFlag;
use App\Models\LoginAudit;
use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\User;
use App\Models\UserPermission;
use App\Services\ActivityLogService;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class SecurityAdminController extends Controller
{
    public function __construct(
        private readonly PermissionService $permissions,
        private readonly ActivityLogService $activity
    ) {
    }

    public function index(Request $request)
    {
        $allPermissions = Permission::query()
            ->where('is_active', true)
            ->orderBy('group_name')
            ->orderBy('name')
            ->get();

        $users = User::query()
            ->whereIn('role', ['admin', 'staff'])
            ->orderBy('name')
            ->get();

        $selectedUser = null;
        $selectedUserPermissions = [];

        if ($request->filled('user_id')) {
            $selectedUser = $users->firstWhere(
                'id',
                (int) $request->integer('user_id')
            );

            if ($selectedUser) {
                $selectedUserPermissions = $this->permissions
                    ->permissionsFor($selectedUser);
            }
        }

        $flags = FeatureFlag::query()
            ->orderBy('name')
            ->get();

        $recentActivity = ActivityLog::query()
            ->with('user')
            ->latest('occurred_at')
            ->limit(40)
            ->get();

        $recentLogins = LoginAudit::query()
            ->with('user')
            ->latest('occurred_at')
            ->limit(40)
            ->get();

        return view('admin.security.index', compact(
            'allPermissions',
            'users',
            'selectedUser',
            'selectedUserPermissions',
            'flags',
            'recentActivity',
            'recentLogins'
        ));
    }

    public function setUserPermission(
        Request $request,
        User $user,
        Permission $permission
    ) {
        $validated = $request->validate([
            'effect' => ['required', 'in:allow,deny,inherit'],
            'expires_at' => ['nullable', 'date'],
        ]);

        if ($validated['effect'] === 'inherit') {
            $this->permissions->removeUserOverride(
                $user,
                $permission
            );
        } else {
            $this->permissions->setUserOverride(
                $user,
                $permission,
                $validated['effect'],
                !empty($validated['expires_at'])
                    ? new \DateTime($validated['expires_at'])
                    : null
            );
        }

        $this->activity->record(
            'permission_override_changed',
            'security',
            $user,
            [],
            [
                'permission' => $permission->code,
                'effect' => $validated['effect'],
            ]
        );

        return back()->with('success', 'تم تحديث صلاحية المستخدم.');
    }

    public function updateFeatureFlag(
        Request $request,
        FeatureFlag $featureFlag
    ) {
        $validated = $request->validate([
            'is_enabled' => ['required', 'boolean'],
            'scope' => ['required', 'in:global,role,user,branch'],
        ]);

        $old = $featureFlag->only([
            'is_enabled',
            'scope',
        ]);

        $featureFlag->update([
            'is_enabled' => (bool) $validated['is_enabled'],
            'scope' => $validated['scope'],
            'updated_by' => auth()->id(),
        ]);

        $this->activity->record(
            'feature_flag_updated',
            'security',
            $featureFlag,
            $old,
            $featureFlag->only([
                'is_enabled',
                'scope',
            ])
        );

        return back()->with('success', 'تم تحديث Feature Flag.');
    }
}

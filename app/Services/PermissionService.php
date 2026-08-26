<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Support\Facades\DB;

class PermissionService
{
    public function can(User $user, string $permissionCode): bool
    {
        $permission = Permission::query()
            ->where('code', $permissionCode)
            ->where('is_active', true)
            ->first();

        if (!$permission) {
            return false;
        }

        $override = UserPermission::query()
            ->where('user_id', $user->id)
            ->where('permission_id', $permission->id)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();

        if ($override) {
            return $override->effect === 'allow';
        }

        return RolePermission::query()
            ->where('role', $user->role)
            ->where('permission_id', $permission->id)
            ->exists();
    }

    public function permissionsFor(User $user): array
    {
        $permissions = Permission::query()
            ->where('is_active', true)
            ->orderBy('group_name')
            ->orderBy('name')
            ->get();

        return $permissions
            ->map(fn (Permission $permission) => [
                'id' => $permission->id,
                'name' => $permission->name,
                'code' => $permission->code,
                'group_name' => $permission->group_name,
                'allowed' => $this->can($user, $permission->code),
            ])
            ->all();
    }

    public function setUserOverride(
        User $user,
        Permission $permission,
        string $effect,
        ?\DateTimeInterface $expiresAt = null
    ): UserPermission {
        return DB::transaction(function () use (
            $user,
            $permission,
            $effect,
            $expiresAt
        ) {
            return UserPermission::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'permission_id' => $permission->id,
                ],
                [
                    'effect' => $effect,
                    'granted_by' => auth()->id(),
                    'expires_at' => $expiresAt,
                ]
            );
        });
    }

    public function removeUserOverride(
        User $user,
        Permission $permission
    ): void {
        UserPermission::query()
            ->where('user_id', $user->id)
            ->where('permission_id', $permission->id)
            ->delete();
    }
}

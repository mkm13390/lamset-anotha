<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeatureFlag extends Model
{
    protected $fillable = [
        'name',
        'key',
        'is_enabled',
        'scope',
        'scope_values',
        'config',
        'description',
        'updated_by',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'scope_values' => 'array',
        'config' => 'array',
    ];

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function isEnabledFor(?User $user = null): bool
    {
        if (!$this->is_enabled) {
            return false;
        }

        if ($this->scope === 'global') {
            return true;
        }

        if (!$user) {
            return false;
        }

        $values = $this->scope_values ?? [];

        return match ($this->scope) {
            'role' => in_array($user->role, $values, true),
            'user' => in_array($user->id, array_map('intval', $values), true),
            default => true,
        };
    }
}

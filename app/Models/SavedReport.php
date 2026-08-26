<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SavedReport extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'report_key',
        'filters',
        'columns',
        'visibility',
        'role_scope',
        'is_favorite',
    ];

    protected $casts = [
        'filters' => 'array',
        'columns' => 'array',
        'is_favorite' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ScheduledReport::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemIncident extends Model
{
    protected $fillable = [
        'incident_number',
        'severity',
        'status',
        'title',
        'description',
        'source',
        'context',
        'assigned_to',
        'resolved_by',
        'detected_at',
        'resolved_at',
    ];

    protected $casts = [
        'context' => 'array',
        'detected_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}

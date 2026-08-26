<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledReport extends Model
{
    protected $fillable = [
        'saved_report_id',
        'created_by',
        'frequency',
        'delivery_channel',
        'recipients',
        'timezone',
        'is_active',
        'last_run_at',
        'next_run_at',
    ];

    protected $casts = [
        'recipients' => 'array',
        'is_active' => 'boolean',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
    ];

    public function savedReport(): BelongsTo
    {
        return $this->belongsTo(SavedReport::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

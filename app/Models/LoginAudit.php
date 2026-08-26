<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginAudit extends Model
{
    protected $fillable = [
        'user_id',
        'email_or_identifier',
        'event_type',
        'ip_address',
        'user_agent',
        'device_name',
        'location_label',
        'is_suspicious',
        'metadata',
        'occurred_at',
    ];

    protected $casts = [
        'is_suspicious' => 'boolean',
        'metadata' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

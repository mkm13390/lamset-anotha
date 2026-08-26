<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSecuritySetting extends Model
{
    protected $fillable = [
        'user_id',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'notify_new_login',
        'notify_suspicious_login',
        'two_factor_confirmed_at',
        'last_security_review_at',
    ];

    protected $casts = [
        'two_factor_enabled' => 'boolean',
        'two_factor_recovery_codes' => 'array',
        'notify_new_login' => 'boolean',
        'notify_suspicious_login' => 'boolean',
        'two_factor_confirmed_at' => 'datetime',
        'last_security_review_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

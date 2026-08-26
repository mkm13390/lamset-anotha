<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemHealthCheck extends Model
{
    protected $fillable = [
        'check_key',
        'check_name',
        'status',
        'response_time_ms',
        'message',
        'details',
        'checked_at',
    ];

    protected $casts = [
        'response_time_ms' => 'integer',
        'details' => 'array',
        'checked_at' => 'datetime',
    ];
}

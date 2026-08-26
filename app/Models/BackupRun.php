<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BackupRun extends Model
{
    protected $fillable = [
        'backup_number',
        'backup_type',
        'status',
        'storage_disk',
        'file_path',
        'size_bytes',
        'checksum',
        'error_message',
        'requested_by',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'size_bytes' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}

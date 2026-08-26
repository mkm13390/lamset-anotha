<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerSegmentMember extends Model
{
    protected $fillable = [
        'customer_segment_id',
        'user_id',
        'score',
        'metadata',
        'added_at',
    ];

    protected $casts = [
        'score' => 'decimal:4',
        'metadata' => 'array',
        'added_at' => 'datetime',
    ];

    public function segment(): BelongsTo
    {
        return $this->belongsTo(
            CustomerSegment::class,
            'customer_segment_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

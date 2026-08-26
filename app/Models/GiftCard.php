<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GiftCard extends Model
{
    protected $fillable = [
        'code',
        'initial_balance',
        'current_balance',
        'purchaser_user_id',
        'assigned_user_id',
        'status',
        'expires_at',
        'last_used_at',
        'recipient_name',
        'recipient_phone',
        'message',
    ];

    protected $casts = [
        'initial_balance' => 'decimal:3',
        'current_balance' => 'decimal:3',
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    public function purchaser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'purchaser_user_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(GiftCardTransaction::class);
    }

    public function isUsable(): bool
    {
        return $this->status === 'active'
            && (float) $this->current_balance > 0
            && (!$this->expires_at || $this->expires_at->isFuture());
    }
}

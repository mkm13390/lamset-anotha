<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class GiftCardTransaction extends Model
{
    protected $fillable = [
        'gift_card_id',
        'type',
        'amount',
        'balance_after',
        'source_type',
        'source_id',
        'reference',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:3',
        'balance_after' => 'decimal:3',
    ];

    public function giftCard(): BelongsTo
    {
        return $this->belongsTo(GiftCard::class);
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

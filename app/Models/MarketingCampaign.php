<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketingCampaign extends Model
{
    protected $fillable = [
        'name',
        'channel',
        'customer_segment_id',
        'promotion_id',
        'status',
        'budget',
        'scheduled_at',
        'started_at',
        'ended_at',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'message_ar',
        'message_en',
        'settings',
    ];

    protected $casts = [
        'budget' => 'decimal:3',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'settings' => 'array',
    ];

    public function segment(): BelongsTo
    {
        return $this->belongsTo(
            CustomerSegment::class,
            'customer_segment_id'
        );
    }

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(MarketingCampaignMember::class);
    }

    public function attributions(): HasMany
    {
        return $this->hasMany(MarketingAttribution::class);
    }

    public function getAttributedRevenueAttribute(): float
    {
        return round((float) $this->attributions()->sum('revenue'), 3);
    }
}

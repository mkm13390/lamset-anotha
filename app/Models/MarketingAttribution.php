<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MarketingAttribution extends Model
{
    protected $fillable = [
        'user_id',
        'marketing_campaign_id',
        'promotion_id',
        'conversion_type',
        'conversion_id',
        'source',
        'medium',
        'campaign',
        'content',
        'revenue',
        'discount_cost',
        'estimated_profit',
        'converted_at',
    ];

    protected $casts = [
        'revenue' => 'decimal:3',
        'discount_cost' => 'decimal:3',
        'estimated_profit' => 'decimal:3',
        'converted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function marketingCampaign(): BelongsTo
    {
        return $this->belongsTo(
            MarketingCampaign::class,
            'marketing_campaign_id'
        );
    }

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    public function conversion(): MorphTo
    {
        return $this->morphTo();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerSegment extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'code',
        'segment_type',
        'rules',
        'is_active',
        'last_refreshed_at',
    ];

    protected $casts = [
        'rules' => 'array',
        'is_active' => 'boolean',
        'last_refreshed_at' => 'datetime',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(CustomerSegmentMember::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(MarketingCampaign::class);
    }
}

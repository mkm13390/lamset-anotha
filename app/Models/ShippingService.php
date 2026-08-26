<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingService extends Model
{
    protected $fillable = [
        'shipping_carrier_id',
        'name_ar',
        'name_en',
        'code',
        'estimated_days_min',
        'estimated_days_max',
        'supports_cod',
        'is_active',
    ];

    protected $casts = [
        'estimated_days_min' => 'integer',
        'estimated_days_max' => 'integer',
        'supports_cod' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(ShippingCarrier::class, 'shipping_carrier_id');
    }

    public function rates(): HasMany
    {
        return $this->hasMany(ShippingRate::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }
}

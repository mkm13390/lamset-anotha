<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingRate extends Model
{
    protected $fillable = [
        'shipping_zone_id',
        'shipping_service_id',
        'base_price',
        'free_shipping_threshold',
        'min_order_amount',
        'max_order_amount',
        'max_weight_kg',
        'extra_kg_price',
        'cod_available',
        'cod_fee',
        'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:3',
        'free_shipping_threshold' => 'decimal:3',
        'min_order_amount' => 'decimal:3',
        'max_order_amount' => 'decimal:3',
        'max_weight_kg' => 'decimal:3',
        'extra_kg_price' => 'decimal:3',
        'cod_available' => 'boolean',
        'cod_fee' => 'decimal:3',
        'is_active' => 'boolean',
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(ShippingZone::class, 'shipping_zone_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(ShippingService::class, 'shipping_service_id');
    }
}

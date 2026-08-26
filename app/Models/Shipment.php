<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    protected $fillable = [
        'shipment_number',
        'order_id',
        'shipping_carrier_id',
        'shipping_service_id',
        'shipping_zone_id',
        'tracking_number',
        'status',
        'shipping_cost',
        'cod_amount',
        'weight_kg',
        'shipped_at',
        'estimated_delivery_at',
        'delivered_at',
        'shipping_address_snapshot',
        'label_path',
        'external_reference',
        'provider_payload',
        'notes',
    ];

    protected $casts = [
        'shipping_cost' => 'decimal:3',
        'cod_amount' => 'decimal:3',
        'weight_kg' => 'decimal:3',
        'shipped_at' => 'datetime',
        'estimated_delivery_at' => 'datetime',
        'delivered_at' => 'datetime',
        'shipping_address_snapshot' => 'array',
        'provider_payload' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(ShippingCarrier::class, 'shipping_carrier_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(ShippingService::class, 'shipping_service_id');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(ShippingZone::class, 'shipping_zone_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(ShipmentEvent::class)->orderBy('occurred_at');
    }

    public function getTrackingUrlAttribute(): ?string
    {
        if (!$this->tracking_number || !$this->carrier?->tracking_url_template) {
            return null;
        }

        return str_replace(
            '{tracking_number}',
            urlencode($this->tracking_number),
            $this->carrier->tracking_url_template
        );
    }
}

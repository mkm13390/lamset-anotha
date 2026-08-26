<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingCarrier extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'code',
        'tracking_url_template',
        'api_base_url',
        'integration_type',
        'supports_cod',
        'supports_return_pickup',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'supports_cod' => 'boolean',
        'supports_return_pickup' => 'boolean',
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    public function services(): HasMany
    {
        return $this->hasMany(ShippingService::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function returnShipments(): HasMany
    {
        return $this->hasMany(ReturnShipment::class);
    }
}

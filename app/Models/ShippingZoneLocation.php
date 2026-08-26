<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingZoneLocation extends Model
{
    protected $fillable = [
        'shipping_zone_id',
        'country_code',
        'governorate',
        'wilayat',
        'postal_code',
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(ShippingZone::class, 'shipping_zone_id');
    }
}

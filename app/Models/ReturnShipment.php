<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnShipment extends Model
{
    protected $fillable = [
        'return_request_id',
        'shipping_carrier_id',
        'tracking_number',
        'status',
        'cost',
        'label_path',
        'external_reference',
        'shipped_at',
        'delivered_at',
        'provider_payload',
    ];

    protected $casts = [
        'cost' => 'decimal:3',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'provider_payload' => 'array',
    ];

    public function returnRequest(): BelongsTo
    {
        return $this->belongsTo(ReturnRequest::class);
    }

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(ShippingCarrier::class, 'shipping_carrier_id');
    }
}

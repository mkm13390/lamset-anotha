<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosHeldSaleItem extends Model
{
    protected $fillable = [
        'pos_held_sale_id',
        'product_variant_id',
        'quantity',
        'unit_price',
        'line_discount',
        'line_total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:3',
        'line_discount' => 'decimal:3',
        'line_total' => 'decimal:3',
    ];

    public function heldSale(): BelongsTo
    {
        return $this->belongsTo(
            PosHeldSale::class,
            'pos_held_sale_id'
        );
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(
            ProductVariant::class,
            'product_variant_id'
        );
    }
}

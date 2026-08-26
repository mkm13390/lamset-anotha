<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosSaleItem extends Model
{
    protected $fillable = [
        'pos_sale_id',
        'product_id',
        'product_variant_id',
        'product_name_ar',
        'product_name_en',
        'sku',
        'color_name_ar',
        'color_name_en',
        'size',
        'quantity',
        'returned_quantity',
        'unit_price',
        'line_discount',
        'line_total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'returned_quantity' => 'integer',
        'unit_price' => 'decimal:3',
        'line_discount' => 'decimal:3',
        'line_total' => 'decimal:3',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(PosSale::class, 'pos_sale_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function returnItems(): HasMany
    {
        return $this->hasMany(PosReturnItem::class, 'pos_sale_item_id');
    }

    public function getReturnableQuantityAttribute(): int
    {
        return max(
            (int) $this->quantity - (int) $this->returned_quantity,
            0
        );
    }

    public function getNetLineTotalAttribute(): float
    {
        return round(
            ((float) $this->unit_price * (int) $this->quantity)
            - (float) $this->line_discount,
            3
        );
    }
}

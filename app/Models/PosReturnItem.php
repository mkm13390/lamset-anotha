<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosReturnItem extends Model
{
    protected $fillable = [
        'pos_return_id',
        'pos_sale_item_id',
        'product_variant_id',
        'quantity',
        'unit_price',
        'line_discount',
        'line_refund',
        'stock_action',
        'reason',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:3',
        'line_discount' => 'decimal:3',
        'line_refund' => 'decimal:3',
    ];

    public function posReturn(): BelongsTo
    {
        return $this->belongsTo(PosReturn::class);
    }

    public function saleItem(): BelongsTo
    {
        return $this->belongsTo(PosSaleItem::class, 'pos_sale_item_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}

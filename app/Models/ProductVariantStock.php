<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariantStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'product_variant_id',
        'quantity',
        'reserved_quantity',
        'damaged_quantity',
        'sample_quantity',
        'reorder_level',
        'reorder_quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'damaged_quantity' => 'integer',
        'sample_quantity' => 'integer',
        'reorder_level' => 'integer',
        'reorder_quantity' => 'integer',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(
            ProductVariant::class,
            'product_variant_id'
        );
    }

    public function getAvailableQuantityAttribute(): int
    {
        return max(
            (int) $this->quantity
            - (int) $this->reserved_quantity
            - (int) $this->damaged_quantity
            - (int) $this->sample_quantity,
            0
        );
    }
}

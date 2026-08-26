<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierReturnItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_return_id',
        'product_variant_id',
        'quantity',
        'unit_cost',
        'line_total',
        'reason',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_cost' => 'decimal:3',
        'line_total' => 'decimal:3',
    ];

    public function supplierReturn(): BelongsTo
    {
        return $this->belongsTo(SupplierReturn::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(
            ProductVariant::class,
            'product_variant_id'
        );
    }
}

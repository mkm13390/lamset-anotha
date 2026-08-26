<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'branch_name',
        'governorate',
        'wilayat',
        'address',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function variantStocks(): HasMany
    {
        return $this->hasMany(ProductVariantStock::class);
    }

    public function outgoingTransfers(): HasMany
    {
        return $this->hasMany(
            StockTransfer::class,
            'from_warehouse_id'
        );
    }

    public function incomingTransfers(): HasMany
    {
        return $this->hasMany(
            StockTransfer::class,
            'to_warehouse_id'
        );
    }

    public function stockCounts(): HasMany
    {
        return $this->hasMany(StockCount::class);
    }
}

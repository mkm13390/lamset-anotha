<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerBehaviorEvent extends Model
{
    protected $fillable = [
        'user_id',
        'session_key',
        'event_type',
        'product_id',
        'product_variant_id',
        'category_id',
        'value',
        'source',
        'device_type',
        'metadata',
        'occurred_at',
    ];

    protected $casts = [
        'value' => 'decimal:3',
        'metadata' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}

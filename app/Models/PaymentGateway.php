<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentGateway extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'code',
        'gateway_type',
        'integration_type',
        'supports_refund',
        'supports_partial_refund',
        'is_active',
        'currency',
        'settings',
    ];

    protected $casts = [
        'supports_refund' => 'boolean',
        'supports_partial_refund' => 'boolean',
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }
}

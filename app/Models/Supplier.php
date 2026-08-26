<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company_name',
        'phone',
        'whatsapp',
        'email',
        'tax_number',
        'commercial_registration',
        'country',
        'governorate',
        'wilayat',
        'address',
        'opening_balance',
        'current_balance',
        'payment_terms_days',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:3',
        'current_balance' => 'decimal:3',
        'payment_terms_days' => 'integer',
        'is_active' => 'boolean',
    ];

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}

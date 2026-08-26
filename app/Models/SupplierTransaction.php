<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'purchase_order_id',
        'supplier_payment_id',
        'user_id',
        'type',
        'transaction_date',
        'debit',
        'credit',
        'balance_after',
        'reference',
        'description',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'debit' => 'decimal:3',
        'credit' => 'decimal:3',
        'balance_after' => 'decimal:3',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(
            SupplierPayment::class,
            'supplier_payment_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

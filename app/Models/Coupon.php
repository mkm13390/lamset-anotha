<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'discount_type',
        'discount_value',
        'minimum_order_amount',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:3',
        'minimum_order_amount' => 'decimal:3',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * هل الكوبون فعّال الآن؟
     */
    public function isCurrentlyValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }

        return true;
    }

    /**
     * هل قيمة السلة تحقق الحد الأدنى للكوبون؟
     */
    public function meetsMinimumOrder(float $subtotal): bool
    {
        return $subtotal >= (float) $this->minimum_order_amount;
    }

    /**
     * حساب قيمة الخصم على مبلغ السلة.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if (!$this->isCurrentlyValid() || !$this->meetsMinimumOrder($subtotal)) {
            return 0;
        }

        if ($this->discount_type === 'percentage') {
            $discount = $subtotal * ((float) $this->discount_value / 100);
        } else {
            $discount = (float) $this->discount_value;
        }

        return round(min($discount, $subtotal), 3);
    }
}

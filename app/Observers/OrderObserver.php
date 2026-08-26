<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\LoyaltyService;

class OrderObserver
{
    public function __construct(
        private LoyaltyService $loyaltyService
    ) {
    }

    /**
     * عند تحديث الطلب، امنح النقاط إذا أصبحت حالته مؤهلة.
     */
    public function updated(Order $order): void
    {
        if (!$order->wasChanged('status')) {
            return;
        }

        $this->loyaltyService->awardForOrder($order->fresh());
    }
}

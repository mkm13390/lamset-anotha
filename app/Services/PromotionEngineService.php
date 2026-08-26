<?php

namespace App\Services;

use App\Models\Promotion;
use App\Models\User;
use Illuminate\Support\Collection;

class PromotionEngineService
{
    public function eligiblePromotions(
        ?User $user,
        float $subtotal,
        int $quantity = 0,
        array $context = []
    ): Collection {
        $now = now();

        return Promotion::query()
            ->where('is_active', true)
            ->where(function ($query) use ($now) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', $now);
            })
            ->get()
            ->filter(function (Promotion $promotion) use (
                $user,
                $subtotal,
                $quantity,
                $context
            ) {
                if ($subtotal < (float) $promotion->minimum_spend) {
                    return false;
                }

                if ($quantity < (int) $promotion->minimum_quantity) {
                    return false;
                }

                $conditions = $promotion->conditions ?: [];

                if (
                    isset($conditions['customer_ids'])
                    && $user
                    && !in_array($user->id, (array) $conditions['customer_ids'])
                ) {
                    return false;
                }

                if (
                    isset($conditions['channels'])
                    && !in_array(
                        $context['channel'] ?? 'web',
                        (array) $conditions['channels'],
                        true
                    )
                ) {
                    return false;
                }

                return true;
            })
            ->values();
    }

    public function calculateDiscount(
        Promotion $promotion,
        float $subtotal
    ): float {
        return match ($promotion->discount_type) {
            'percentage' => round(
                $subtotal * ((float) $promotion->discount_value / 100),
                3
            ),
            'fixed_amount' => round(
                min((float) $promotion->discount_value, $subtotal),
                3
            ),
            default => 0,
        };
    }
}

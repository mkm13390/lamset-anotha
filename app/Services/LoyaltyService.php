<?php

namespace App\Services;

use App\Models\LoyaltyAccount;
use App\Models\LoyaltyTransaction;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class LoyaltyService
{
    /**
     * منح نقاط الولاء لطلب مكتمل.
     *
     * يتم منح النقاط مرة واحدة فقط لكل طلب،
     * وللطلبات المرتبطة بمستخدم مسجل فقط.
     */
    public function awardForOrder(Order $order): int
    {
        $eligibleStatuses = config(
            'loyalty.earning_order_statuses',
            ['delivered']
        );

        if (!$order->user_id) {
            return 0;
        }

        if (!in_array($order->status, $eligibleStatuses, true)) {
            return 0;
        }

        return DB::transaction(function () use ($order) {

            /*
             |----------------------------------------------------------
             | منع تكرار منح النقاط لنفس الطلب
             |----------------------------------------------------------
             */
            $alreadyAwarded = LoyaltyTransaction::query()
                ->where('order_id', $order->id)
                ->where('type', 'earn')
                ->lockForUpdate()
                ->exists();

            if ($alreadyAwarded) {
                return 0;
            }

            /*
             |----------------------------------------------------------
             | المبلغ المؤهل للنقاط
             |----------------------------------------------------------
             | نستبعد رسوم التوصيل ونحسب على قيمة المنتجات
             | بعد الخصم.
             */
            $subtotal = (float) ($order->subtotal ?? 0);
            $discount = (float) ($order->discount_amount ?? 0);

            $eligibleAmount = max(
                $subtotal - $discount,
                0
            );

            $rate = (float) config(
                'loyalty.earn_points_per_omr',
                1
            );

            $points = (int) floor(
                $eligibleAmount * $rate
            );

            if ($points <= 0) {
                return 0;
            }

            /*
             |----------------------------------------------------------
             | إنشاء أو جلب حساب الولاء
             |----------------------------------------------------------
             */
            $account = LoyaltyAccount::query()
                ->where('user_id', $order->user_id)
                ->lockForUpdate()
                ->first();

            if (!$account) {
                LoyaltyAccount::create([
                    'user_id' => $order->user_id,
                    'points_balance' => 0,
                    'total_points_earned' => 0,
                    'total_points_redeemed' => 0,
                ]);

                $account = LoyaltyAccount::query()
                    ->where('user_id', $order->user_id)
                    ->lockForUpdate()
                    ->firstOrFail();
            }

            $newBalance = $account->points_balance + $points;

            $account->update([
                'points_balance' => $newBalance,
                'total_points_earned' =>
                    $account->total_points_earned + $points,
            ]);

            /*
             |----------------------------------------------------------
             | تاريخ انتهاء النقاط
             |----------------------------------------------------------
             */
            $expireDays = config(
                'loyalty.points_expire_after_days'
            );

            $expiresAt = $expireDays
                ? now()->addDays((int) $expireDays)
                : null;

            LoyaltyTransaction::create([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'type' => 'earn',
                'points' => $points,
                'balance_after' => $newBalance,
                'reference' => $order->order_number,
                'description' => 'نقاط مكتسبة من طلب مكتمل',
                'expires_at' => $expiresAt,
            ]);

            return $points;
        });
    }

    /**
     * جلب رصيد نقاط المستخدم.
     */
    public function balanceForUser(int $userId): int
    {
        return (int) (
            LoyaltyAccount::query()
                ->where('user_id', $userId)
                ->value('points_balance')
            ?? 0
        );
    }
}

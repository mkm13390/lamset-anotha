<?php

namespace App\Services;

use App\Models\CustomerLoyaltyProfile;
use App\Models\LoyaltyTier;
use App\Models\StoreCreditTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoyaltyMarketingService
{
    public function profileFor(User $user): CustomerLoyaltyProfile
    {
        return DB::transaction(function () use ($user) {
            $profile = CustomerLoyaltyProfile::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if ($profile) {
                return $profile->load('tier');
            }

            $tier = LoyaltyTier::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->first();

            do {
                $code = strtoupper(Str::random(8));
            } while (
                CustomerLoyaltyProfile::where('referral_code', $code)->exists()
            );

            $profile = CustomerLoyaltyProfile::create([
                'user_id' => $user->id,
                'loyalty_tier_id' => $tier?->id,
                'points_balance' => 0,
                'lifetime_points' => 0,
                'lifetime_spend' => 0,
                'store_credit_balance' => 0,
                'referral_code' => $code,
                'tier_updated_at' => now(),
            ]);

            return $profile->load('tier');
        });
    }

    public function awardPoints(
        User $user,
        int $points,
        float $spend = 0
    ): CustomerLoyaltyProfile {
        if ($points < 0) {
            throw ValidationException::withMessages([
                'points' => 'عدد النقاط غير صالح.',
            ]);
        }

        return DB::transaction(function () use ($user, $points, $spend) {
            $profile = $this->profileFor($user);

            $profile = CustomerLoyaltyProfile::query()
                ->lockForUpdate()
                ->findOrFail($profile->id);

            $profile->update([
                'points_balance' => (int) $profile->points_balance + $points,
                'lifetime_points' => (int) $profile->lifetime_points + $points,
                'lifetime_spend' => round(
                    (float) $profile->lifetime_spend + max($spend, 0),
                    3
                ),
                'last_purchase_at' => $spend > 0 ? now() : $profile->last_purchase_at,
            ]);

            $this->refreshTier($profile);

            return $profile->refresh()->load('tier');
        });
    }

    public function addStoreCredit(
        User $user,
        float $amount,
        string $type = 'reward',
        ?string $reference = null,
        ?string $description = null
    ): CustomerLoyaltyProfile {
        $amount = round($amount, 3);

        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'قيمة رصيد المتجر يجب أن تكون أكبر من صفر.',
            ]);
        }

        return DB::transaction(function () use (
            $user,
            $amount,
            $type,
            $reference,
            $description
        ) {
            $profile = $this->profileFor($user);

            $profile = CustomerLoyaltyProfile::query()
                ->lockForUpdate()
                ->findOrFail($profile->id);

            $newBalance = round(
                (float) $profile->store_credit_balance + $amount,
                3
            );

            $profile->update([
                'store_credit_balance' => $newBalance,
            ]);

            StoreCreditTransaction::create([
                'user_id' => $user->id,
                'type' => $type,
                'amount' => $amount,
                'balance_after' => $newBalance,
                'reference' => $reference,
                'description' => $description,
                'created_by' => auth()->id(),
            ]);

            return $profile->refresh();
        });
    }

    public function deductStoreCredit(
        User $user,
        float $amount,
        ?string $reference = null,
        ?string $description = null
    ): CustomerLoyaltyProfile {
        $amount = round($amount, 3);

        return DB::transaction(function () use (
            $user,
            $amount,
            $reference,
            $description
        ) {
            $profile = $this->profileFor($user);

            $profile = CustomerLoyaltyProfile::query()
                ->lockForUpdate()
                ->findOrFail($profile->id);

            if ((float) $profile->store_credit_balance < $amount) {
                throw ValidationException::withMessages([
                    'store_credit' => 'رصيد المتجر غير كافٍ.',
                ]);
            }

            $newBalance = round(
                (float) $profile->store_credit_balance - $amount,
                3
            );

            $profile->update([
                'store_credit_balance' => $newBalance,
            ]);

            StoreCreditTransaction::create([
                'user_id' => $user->id,
                'type' => 'debit',
                'amount' => $amount,
                'balance_after' => $newBalance,
                'reference' => $reference,
                'description' => $description,
                'created_by' => auth()->id(),
            ]);

            return $profile->refresh();
        });
    }

    public function refreshTier(CustomerLoyaltyProfile $profile): void
    {
        $tier = LoyaltyTier::query()
            ->where('is_active', true)
            ->where('min_spend', '<=', $profile->lifetime_spend)
            ->where('min_points', '<=', $profile->lifetime_points)
            ->orderByDesc('min_spend')
            ->orderByDesc('min_points')
            ->first();

        if ($tier && (int) $profile->loyalty_tier_id !== (int) $tier->id) {
            $profile->update([
                'loyalty_tier_id' => $tier->id,
                'tier_updated_at' => now(),
            ]);
        }
    }
}

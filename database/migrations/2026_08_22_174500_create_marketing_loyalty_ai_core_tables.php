<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | loyalty_tiers
        |--------------------------------------------------------------------------
        | مستويات الولاء مثل Silver / Gold / Platinum.
        */
        Schema::create('loyalty_tiers', function (Blueprint $table) {
            $table->id();

            $table->string('name_ar', 120);
            $table->string('name_en', 120)->nullable();
            $table->string('code', 80)->unique();

            $table->decimal('min_spend', 14, 3)->default(0);
            $table->unsignedInteger('min_points')->default(0);

            $table->decimal('points_multiplier', 8, 3)->default(1);
            $table->decimal('discount_percent', 8, 3)->default(0);

            $table->boolean('free_shipping')->default(false);
            $table->boolean('early_access')->default(false);
            $table->boolean('is_active')->default(true);

            $table->unsignedInteger('sort_order')->default(0);

            $table->json('benefits')->nullable();
            $table->timestamps();

            $table->index('is_active');
            $table->index('sort_order');
        });

        /*
        |--------------------------------------------------------------------------
        | customer_loyalty_profiles
        |--------------------------------------------------------------------------
        | ملف الولاء الموحد للعميل.
        */
        Schema::create('customer_loyalty_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('loyalty_tier_id')
                ->nullable()
                ->constrained('loyalty_tiers')
                ->nullOnDelete();

            $table->unsignedInteger('points_balance')->default(0);
            $table->unsignedInteger('lifetime_points')->default(0);

            $table->decimal('lifetime_spend', 14, 3)->default(0);
            $table->decimal('store_credit_balance', 14, 3)->default(0);

            $table->string('referral_code', 80)->unique();

            $table->timestamp('tier_updated_at')->nullable();
            $table->timestamp('last_purchase_at')->nullable();

            $table->timestamps();

            $table->index('points_balance');
            $table->index('lifetime_spend');
            $table->index('last_purchase_at');
        });

        /*
        |--------------------------------------------------------------------------
        | store_credit_transactions
        |--------------------------------------------------------------------------
        | رصيد المتجر: إضافة، استخدام، استرجاع، مكافأة أو تسوية.
        */
        Schema::create('store_credit_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('type', [
                'credit',
                'debit',
                'refund',
                'reward',
                'adjustment',
                'expiry',
            ]);

            $table->decimal('amount', 14, 3);
            $table->decimal('balance_after', 14, 3);

            $table->nullableMorphs('source');

            $table->string('reference', 150)->nullable();
            $table->string('description', 500)->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('expires_at');
        });

        /*
        |--------------------------------------------------------------------------
        | referrals
        |--------------------------------------------------------------------------
        | برنامج إحالة عميل لعميل.
        */
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('referrer_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('referred_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('referral_code', 80);

            $table->enum('status', [
                'clicked',
                'registered',
                'qualified',
                'rewarded',
                'cancelled',
            ])->default('clicked');

            $table->decimal('order_value', 14, 3)->default(0);
            $table->decimal('referrer_reward', 14, 3)->default(0);
            $table->decimal('referred_reward', 14, 3)->default(0);

            $table->foreignId('order_id')
                ->nullable()
                ->constrained('orders')
                ->nullOnDelete();

            $table->timestamp('qualified_at')->nullable();
            $table->timestamp('rewarded_at')->nullable();

            $table->timestamps();

            $table->index('referral_code');
            $table->index('status');
        });

        /*
        |--------------------------------------------------------------------------
        | gift_cards
        |--------------------------------------------------------------------------
        */
        Schema::create('gift_cards', function (Blueprint $table) {
            $table->id();

            $table->string('code', 120)->unique();

            $table->decimal('initial_balance', 14, 3);
            $table->decimal('current_balance', 14, 3);

            $table->foreignId('purchaser_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('assigned_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('status', [
                'active',
                'used',
                'expired',
                'cancelled',
            ])->default('active');

            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_used_at')->nullable();

            $table->string('recipient_name', 150)->nullable();
            $table->string('recipient_phone', 50)->nullable();
            $table->string('message', 500)->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('expires_at');
        });

        Schema::create('gift_card_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('gift_card_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('type', [
                'issue',
                'redeem',
                'refund',
                'adjustment',
            ]);

            $table->decimal('amount', 14, 3);
            $table->decimal('balance_after', 14, 3);

            $table->nullableMorphs('source');

            $table->string('reference', 150)->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('type');
        });

        /*
        |--------------------------------------------------------------------------
        | product_bundles
        |--------------------------------------------------------------------------
        | باقات منتجات قابلة للبيع والتسويق.
        */
        Schema::create('product_bundles', function (Blueprint $table) {
            $table->id();

            $table->string('name_ar', 180);
            $table->string('name_en', 180)->nullable();
            $table->string('slug', 220)->unique();

            $table->enum('pricing_type', [
                'fixed_price',
                'percentage_discount',
                'amount_discount',
            ])->default('fixed_price');

            $table->decimal('value', 14, 3)->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();

            $table->timestamps();

            $table->index('is_active');
            $table->index(['starts_at', 'ends_at']);
        });

        Schema::create('product_bundle_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_bundle_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_variant_id')
                ->constrained('product_variants')
                ->restrictOnDelete();

            $table->unsignedInteger('quantity')->default(1);

            $table->timestamps();

            $table->unique(
                ['product_bundle_id', 'product_variant_id'],
                'bundle_variant_unique'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | promotions
        |--------------------------------------------------------------------------
        | محرك عروض ذكي مرن بدل hard-code لكل عرض.
        */
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();

            $table->string('name_ar', 180);
            $table->string('name_en', 180)->nullable();
            $table->string('code', 100)->nullable()->unique();

            $table->enum('promotion_type', [
                'automatic',
                'coupon',
                'flash_deal',
                'bundle',
                'loyalty',
                'win_back',
                'vip',
                'referral',
            ])->default('automatic');

            $table->enum('discount_type', [
                'percentage',
                'fixed_amount',
                'free_shipping',
                'bonus_points',
                'store_credit',
                'gift_item',
            ]);

            $table->decimal('discount_value', 14, 3)->default(0);

            $table->decimal('minimum_spend', 14, 3)->default(0);
            $table->unsignedInteger('minimum_quantity')->default(0);

            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('per_customer_limit')->nullable();

            $table->boolean('is_stackable')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->json('conditions')->nullable();
            $table->json('actions')->nullable();

            $table->timestamps();

            $table->index('promotion_type');
            $table->index('is_active');
            $table->index(['starts_at', 'ends_at']);
        });

        Schema::create('promotion_usages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('promotion_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('order_id')
                ->nullable()
                ->constrained('orders')
                ->nullOnDelete();

            $table->foreignId('pos_sale_id')
                ->nullable()
                ->constrained('pos_sales')
                ->nullOnDelete();

            $table->decimal('discount_amount', 14, 3)->default(0);
            $table->decimal('reward_value', 14, 3)->default(0);

            $table->timestamp('used_at');
            $table->timestamps();

            $table->index('used_at');
        });

        /*
        |--------------------------------------------------------------------------
        | customer_segments
        |--------------------------------------------------------------------------
        | شرائح ثابتة أو ذكية.
        */
        Schema::create('customer_segments', function (Blueprint $table) {
            $table->id();

            $table->string('name_ar', 160);
            $table->string('name_en', 160)->nullable();
            $table->string('code', 100)->unique();

            $table->enum('segment_type', [
                'manual',
                'rule_based',
                'ai',
            ])->default('rule_based');

            $table->json('rules')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamp('last_refreshed_at')->nullable();

            $table->timestamps();

            $table->index('segment_type');
            $table->index('is_active');
        });

        Schema::create('customer_segment_members', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_segment_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('score', 10, 4)->nullable();

            $table->json('metadata')->nullable();
            $table->timestamp('added_at')->useCurrent();

            $table->timestamps();

            $table->unique(
                ['customer_segment_id', 'user_id'],
                'segment_user_unique'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | marketing_campaigns
        |--------------------------------------------------------------------------
        */
        Schema::create('marketing_campaigns', function (Blueprint $table) {
            $table->id();

            $table->string('name', 180);

            $table->enum('channel', [
                'whatsapp',
                'email',
                'sms',
                'push',
                'instagram',
                'web',
                'mixed',
            ])->default('mixed');

            $table->foreignId('customer_segment_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('promotion_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->enum('status', [
                'draft',
                'scheduled',
                'running',
                'paused',
                'completed',
                'cancelled',
            ])->default('draft');

            $table->decimal('budget', 14, 3)->default(0);

            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();

            $table->string('utm_source', 120)->nullable();
            $table->string('utm_medium', 120)->nullable();
            $table->string('utm_campaign', 120)->nullable();

            $table->text('message_ar')->nullable();
            $table->text('message_en')->nullable();

            $table->json('settings')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('channel');
            $table->index('scheduled_at');
        });

        Schema::create('marketing_campaign_members', function (Blueprint $table) {
            $table->id();

            $table->foreignId('marketing_campaign_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->enum('status', [
                'pending',
                'sent',
                'delivered',
                'opened',
                'clicked',
                'converted',
                'failed',
                'unsubscribed',
            ])->default('pending');

            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->timestamp('converted_at')->nullable();

            $table->decimal('revenue', 14, 3)->default(0);

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index('status');
        });

        /*
        |--------------------------------------------------------------------------
        | marketing_attributions
        |--------------------------------------------------------------------------
        | قياس مصدر الطلب والعائد على الحملة.
        */
        Schema::create('marketing_attributions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('marketing_campaign_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('promotion_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->nullableMorphs('conversion');

            $table->string('source', 120)->nullable();
            $table->string('medium', 120)->nullable();
            $table->string('campaign', 120)->nullable();
            $table->string('content', 120)->nullable();

            $table->decimal('revenue', 14, 3)->default(0);
            $table->decimal('discount_cost', 14, 3)->default(0);
            $table->decimal('estimated_profit', 14, 3)->default(0);

            $table->timestamp('converted_at')->nullable();

            $table->timestamps();

            $table->index('source');
            $table->index('converted_at');
        });

        /*
        |--------------------------------------------------------------------------
        | abandoned_carts
        |--------------------------------------------------------------------------
        */
        Schema::create('abandoned_carts', function (Blueprint $table) {
            $table->id();

            $table->string('session_key', 160)->nullable();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('customer_name', 150)->nullable();
            $table->string('customer_phone', 50)->nullable();
            $table->string('customer_email', 180)->nullable();

            $table->decimal('subtotal', 14, 3)->default(0);
            $table->decimal('total', 14, 3)->default(0);

            $table->enum('status', [
                'active',
                'abandoned',
                'recovered',
                'expired',
            ])->default('active');

            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('abandoned_at')->nullable();
            $table->timestamp('recovered_at')->nullable();

            $table->timestamps();

            $table->index('session_key');
            $table->index('status');
            $table->index('last_activity_at');
        });

        Schema::create('abandoned_cart_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('abandoned_cart_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_variant_id')
                ->constrained('product_variants')
                ->restrictOnDelete();

            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 14, 3);

            $table->timestamps();

            $table->unique(
                ['abandoned_cart_id', 'product_variant_id'],
                'abandoned_cart_variant_unique'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | product_alert_subscriptions
        |--------------------------------------------------------------------------
        | رجع للمخزون / انخفاض سعر / قرب نفاد.
        */
        Schema::create('product_alert_subscriptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('product_variant_id')
                ->constrained('product_variants')
                ->cascadeOnDelete();

            $table->string('phone', 50)->nullable();
            $table->string('email', 180)->nullable();

            $table->enum('alert_type', [
                'back_in_stock',
                'price_drop',
                'low_stock',
            ]);

            $table->enum('channel', [
                'whatsapp',
                'email',
                'sms',
                'push',
            ])->default('whatsapp');

            $table->boolean('is_active')->default(true);
            $table->timestamp('last_notified_at')->nullable();

            $table->timestamps();

            $table->index('alert_type');
            $table->index('is_active');
        });

        /*
        |--------------------------------------------------------------------------
        | customer_behavior_events
        |--------------------------------------------------------------------------
        | وقود الذكاء الاصطناعي: view, search, wishlist, cart, purchase...
        */
        Schema::create('customer_behavior_events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('session_key', 160)->nullable();

            $table->string('event_type', 100);

            $table->foreignId('product_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->nullOnDelete();

            $table->foreignId('category_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->decimal('value', 14, 3)->nullable();

            $table->string('source', 120)->nullable();
            $table->string('device_type', 80)->nullable();

            $table->json('metadata')->nullable();

            $table->timestamp('occurred_at')->useCurrent();
            $table->timestamps();

            $table->index('event_type');
            $table->index('session_key');
            $table->index('occurred_at');
            $table->index(['user_id', 'event_type']);
        });

        /*
        |--------------------------------------------------------------------------
        | ai_customer_marketing_profiles
        |--------------------------------------------------------------------------
        | مخرجات AI المستقبلية. لا يتم تعبئتها الآن إلا عند تشغيل النماذج.
        */
        Schema::create('ai_customer_marketing_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('purchase_probability', 8, 6)->nullable();
            $table->decimal('churn_probability', 8, 6)->nullable();
            $table->decimal('discount_sensitivity', 8, 6)->nullable();
            $table->decimal('predicted_lifetime_value', 14, 3)->nullable();

            $table->string('preferred_channel', 80)->nullable();
            $table->string('preferred_send_window', 80)->nullable();

            $table->json('top_categories')->nullable();
            $table->json('top_products')->nullable();
            $table->json('next_best_actions')->nullable();

            $table->string('model_version', 120)->nullable();
            $table->timestamp('scored_at')->nullable();

            $table->timestamps();

            $table->index('churn_probability');
            $table->index('purchase_probability');
            $table->index('predicted_lifetime_value');
        });

        /*
        |--------------------------------------------------------------------------
        | Defaults
        |--------------------------------------------------------------------------
        */
        DB::table('loyalty_tiers')->insert([
            [
                'name_ar' => 'فضي',
                'name_en' => 'Silver',
                'code' => 'silver',
                'min_spend' => 0,
                'min_points' => 0,
                'points_multiplier' => 1.000,
                'discount_percent' => 0,
                'free_shipping' => false,
                'early_access' => false,
                'is_active' => true,
                'sort_order' => 10,
                'benefits' => json_encode(['base_loyalty']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_ar' => 'ذهبي',
                'name_en' => 'Gold',
                'code' => 'gold',
                'min_spend' => 250,
                'min_points' => 0,
                'points_multiplier' => 1.250,
                'discount_percent' => 0,
                'free_shipping' => false,
                'early_access' => true,
                'is_active' => true,
                'sort_order' => 20,
                'benefits' => json_encode(['early_access']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_ar' => 'بلاتيني',
                'name_en' => 'Platinum',
                'code' => 'platinum',
                'min_spend' => 600,
                'min_points' => 0,
                'points_multiplier' => 1.500,
                'discount_percent' => 0,
                'free_shipping' => true,
                'early_access' => true,
                'is_active' => true,
                'sort_order' => 30,
                'benefits' => json_encode(['early_access', 'free_shipping']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_customer_marketing_profiles');
        Schema::dropIfExists('customer_behavior_events');
        Schema::dropIfExists('product_alert_subscriptions');
        Schema::dropIfExists('abandoned_cart_items');
        Schema::dropIfExists('abandoned_carts');
        Schema::dropIfExists('marketing_attributions');
        Schema::dropIfExists('marketing_campaign_members');
        Schema::dropIfExists('marketing_campaigns');
        Schema::dropIfExists('customer_segment_members');
        Schema::dropIfExists('customer_segments');
        Schema::dropIfExists('promotion_usages');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('product_bundle_items');
        Schema::dropIfExists('product_bundles');
        Schema::dropIfExists('gift_card_transactions');
        Schema::dropIfExists('gift_cards');
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('store_credit_transactions');
        Schema::dropIfExists('customer_loyalty_profiles');
        Schema::dropIfExists('loyalty_tiers');
    }
};

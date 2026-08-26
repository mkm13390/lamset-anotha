<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_carriers', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar', 160);
            $table->string('name_en', 160)->nullable();
            $table->string('code', 80)->unique();
            $table->string('tracking_url_template', 500)->nullable();
            $table->string('api_base_url', 500)->nullable();
            $table->enum('integration_type', [
                'manual',
                'api',
                'webhook',
                'aggregator',
            ])->default('manual');
            $table->boolean('supports_cod')->default(false);
            $table->boolean('supports_return_pickup')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->index('is_active');
        });

        Schema::create('shipping_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_carrier_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->string('name_ar', 160);
            $table->string('name_en', 160)->nullable();
            $table->string('code', 100)->unique();
            $table->unsignedInteger('estimated_days_min')->nullable();
            $table->unsignedInteger('estimated_days_max')->nullable();
            $table->boolean('supports_cod')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('is_active');
        });

        Schema::create('shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar', 160);
            $table->string('name_en', 160)->nullable();
            $table->string('code', 100)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('is_active');
        });

        Schema::create('shipping_zone_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_zone_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('country_code', 2)->default('OM');
            $table->string('governorate', 160)->nullable();
            $table->string('wilayat', 160)->nullable();
            $table->string('postal_code', 30)->nullable();
            $table->timestamps();
            $table->index(['country_code', 'governorate']);
            $table->index(['governorate', 'wilayat']);
        });

        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_zone_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('shipping_service_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->decimal('base_price', 14, 3)->default(0);
            $table->decimal('free_shipping_threshold', 14, 3)->nullable();
            $table->decimal('min_order_amount', 14, 3)->default(0);
            $table->decimal('max_order_amount', 14, 3)->nullable();
            $table->decimal('max_weight_kg', 10, 3)->nullable();
            $table->decimal('extra_kg_price', 14, 3)->default(0);
            $table->boolean('cod_available')->default(false);
            $table->decimal('cod_fee', 14, 3)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('is_active');
            $table->unique(
                ['shipping_zone_id', 'shipping_service_id'],
                'shipping_zone_service_unique'
            );
        });

        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_number', 120)->unique();
            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('shipping_carrier_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('shipping_service_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('shipping_zone_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->string('tracking_number', 180)->nullable()->index();
            $table->enum('status', [
                'pending',
                'ready',
                'picked_up',
                'in_transit',
                'out_for_delivery',
                'delivered',
                'failed_delivery',
                'returned_to_sender',
                'cancelled',
            ])->default('pending');
            $table->decimal('shipping_cost', 14, 3)->default(0);
            $table->decimal('cod_amount', 14, 3)->default(0);
            $table->decimal('weight_kg', 10, 3)->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('estimated_delivery_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->json('shipping_address_snapshot')->nullable();
            $table->string('label_path', 500)->nullable();
            $table->string('external_reference', 180)->nullable();
            $table->json('provider_payload')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('status');
            $table->index('created_at');
        });

        Schema::create('shipment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('order_item_id')
                ->constrained()
                ->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->timestamps();
            $table->unique(
                ['shipment_id', 'order_item_id'],
                'shipment_order_item_unique'
            );
        });

        Schema::create('shipment_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('status', 100);
            $table->string('location', 255)->nullable();
            $table->string('description', 500)->nullable();
            $table->string('external_event_id', 180)->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();
            $table->index(['shipment_id', 'occurred_at']);
        });

        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar', 160);
            $table->string('name_en', 160)->nullable();
            $table->string('code', 100)->unique();
            $table->enum('gateway_type', [
                'cash',
                'cod',
                'card',
                'bank_transfer',
                'online_gateway',
                'store_credit',
                'gift_card',
                'other',
            ]);
            $table->enum('integration_type', [
                'manual',
                'redirect',
                'api',
                'hosted_fields',
                'app_to_app',
            ])->default('manual');
            $table->boolean('supports_refund')->default(false);
            $table->boolean('supports_partial_refund')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('currency', 3)->default('OMR');
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->index('gateway_type');
            $table->index('is_active');
        });

        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number', 120)->unique();
            $table->foreignId('payment_gateway_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->nullableMorphs('payable');
            $table->enum('type', [
                'authorization',
                'capture',
                'payment',
                'refund',
                'void',
                'chargeback',
                'adjustment',
            ])->default('payment');
            $table->enum('status', [
                'pending',
                'processing',
                'authorized',
                'paid',
                'partially_refunded',
                'refunded',
                'failed',
                'cancelled',
                'voided',
            ])->default('pending');
            $table->decimal('amount', 14, 3);
            $table->decimal('refunded_amount', 14, 3)->default(0);
            $table->string('currency', 3)->default('OMR');
            $table->string('external_transaction_id', 180)->nullable()->index();
            $table->string('approval_code', 120)->nullable();
            $table->string('bank_reference', 180)->nullable();
            $table->string('rrn', 120)->nullable();
            $table->string('card_scheme', 80)->nullable();
            $table->string('masked_card', 50)->nullable();
            $table->timestamp('authorized_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->string('failure_code', 120)->nullable();
            $table->string('failure_message', 500)->nullable();
            $table->timestamps();
            $table->index('status');
            $table->index('type');
            $table->index('created_at');
        });

        Schema::create('return_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar', 180);
            $table->string('name_en', 180)->nullable();
            $table->unsignedInteger('return_window_days')->default(14);
            $table->unsignedInteger('exchange_window_days')->default(14);
            $table->boolean('allow_refund')->default(true);
            $table->boolean('allow_exchange')->default(true);
            $table->boolean('allow_store_credit')->default(true);
            $table->boolean('customer_pays_return_shipping')->default(false);
            $table->boolean('require_original_packaging')->default(false);
            $table->json('excluded_category_ids')->nullable();
            $table->json('excluded_product_ids')->nullable();
            $table->text('terms_ar')->nullable();
            $table->text('terms_en')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('is_active');
        });

        Schema::create('return_requests', function (Blueprint $table) {
            $table->id();
            $table->string('return_number', 120)->unique();
            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('return_policy_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('replacement_order_id')
                ->nullable()
                ->constrained('orders')
                ->nullOnDelete();
            $table->enum('request_type', [
                'refund',
                'exchange',
                'store_credit',
            ])->default('refund');
            $table->enum('status', [
                'requested',
                'under_review',
                'approved',
                'rejected',
                'awaiting_return',
                'in_transit',
                'received',
                'inspected',
                'refunded',
                'exchanged',
                'store_credited',
                'closed',
                'cancelled',
            ])->default('requested');
            $table->string('reason_code', 100)->nullable();
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->decimal('requested_amount', 14, 3)->default(0);
            $table->decimal('approved_amount', 14, 3)->default(0);
            $table->decimal('return_shipping_cost', 14, 3)->default(0);
            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('requested_at');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->index('status');
            $table->index('request_type');
            $table->index('requested_at');
        });

        Schema::create('return_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_request_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('order_item_id')
                ->constrained()
                ->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->enum('condition', [
                'unopened',
                'new',
                'used',
                'damaged',
                'defective',
                'wrong_item',
                'other',
            ])->default('new');
            $table->string('reason_code', 100)->nullable();
            $table->text('notes')->nullable();
            $table->decimal('unit_refund_amount', 14, 3)->default(0);
            $table->decimal('approved_refund_amount', 14, 3)->default(0);
            $table->boolean('restock')->default(false);
            $table->boolean('mark_damaged')->default(false);
            $table->timestamps();
            $table->unique(
                ['return_request_id', 'order_item_id'],
                'return_request_order_item_unique'
            );
        });

        Schema::create('return_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_request_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('shipping_carrier_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->string('tracking_number', 180)->nullable()->index();
            $table->enum('status', [
                'pending',
                'label_created',
                'picked_up',
                'in_transit',
                'delivered',
                'failed',
                'cancelled',
            ])->default('pending');
            $table->decimal('cost', 14, 3)->default(0);
            $table->string('label_path', 500)->nullable();
            $table->string('external_reference', 180)->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->json('provider_payload')->nullable();
            $table->timestamps();
            $table->index('status');
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->string('refund_number', 120)->unique();
            $table->foreignId('return_request_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('payment_transaction_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('processed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->enum('refund_method', [
                'original_payment',
                'cash',
                'bank_transfer',
                'store_credit',
                'gift_card',
                'other',
            ]);
            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'failed',
                'cancelled',
            ])->default('pending');
            $table->decimal('amount', 14, 3);
            $table->string('currency', 3)->default('OMR');
            $table->string('external_refund_id', 180)->nullable();
            $table->string('reference', 180)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->index('status');
            $table->index('refund_method');
        });

        DB::table('return_policies')->insert([
            'name_ar' => 'السياسة الافتراضية',
            'name_en' => 'Default Return Policy',
            'return_window_days' => 14,
            'exchange_window_days' => 14,
            'allow_refund' => true,
            'allow_exchange' => true,
            'allow_store_credit' => true,
            'customer_pays_return_shipping' => false,
            'require_original_packaging' => false,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('return_shipments');
        Schema::dropIfExists('return_request_items');
        Schema::dropIfExists('return_requests');
        Schema::dropIfExists('return_policies');
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('payment_gateways');
        Schema::dropIfExists('shipment_events');
        Schema::dropIfExists('shipment_items');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('shipping_rates');
        Schema::dropIfExists('shipping_zone_locations');
        Schema::dropIfExists('shipping_zones');
        Schema::dropIfExists('shipping_services');
        Schema::dropIfExists('shipping_carriers');
    }
};

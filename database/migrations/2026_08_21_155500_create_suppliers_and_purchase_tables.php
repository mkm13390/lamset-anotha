<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | suppliers
        |--------------------------------------------------------------------------
        | الموردون الأساسيون للنظام.
        */
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();

            $table->string('name', 180);
            $table->string('company_name', 180)->nullable();

            $table->string('phone', 30)->nullable();
            $table->string('whatsapp', 30)->nullable();
            $table->string('email', 190)->nullable();

            $table->string('tax_number', 100)->nullable();
            $table->string('commercial_registration', 100)->nullable();

            $table->string('country', 100)->default('Oman');
            $table->string('governorate', 100)->nullable();
            $table->string('wilayat', 100)->nullable();
            $table->text('address')->nullable();

            $table->decimal('opening_balance', 14, 3)->default(0);
            $table->decimal('current_balance', 14, 3)->default(0);

            $table->unsignedInteger('payment_terms_days')->default(0);

            $table->boolean('is_active')->default(true);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('name');
            $table->index('phone');
            $table->index('email');
            $table->index('is_active');
        });

        /*
        |--------------------------------------------------------------------------
        | purchase_orders
        |--------------------------------------------------------------------------
        | أوامر الشراء من الموردين.
        */
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();

            $table->string('purchase_number', 100)->unique();

            $table->foreignId('supplier_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->date('order_date');
            $table->date('expected_date')->nullable();
            $table->date('received_date')->nullable();

            $table->enum('status', [
                'draft',
                'ordered',
                'partially_received',
                'received',
                'cancelled',
            ])->default('draft');

            $table->enum('payment_status', [
                'unpaid',
                'partial',
                'paid',
            ])->default('unpaid');

            $table->string('currency', 10)->default('OMR');

            $table->decimal('subtotal', 14, 3)->default(0);
            $table->decimal('discount_amount', 14, 3)->default(0);
            $table->decimal('shipping_amount', 14, 3)->default(0);
            $table->decimal('tax_amount', 14, 3)->default(0);
            $table->decimal('total', 14, 3)->default(0);

            $table->decimal('paid_amount', 14, 3)->default(0);
            $table->decimal('balance_due', 14, 3)->default(0);

            $table->string('supplier_invoice_number', 120)->nullable();
            $table->string('reference', 150)->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('supplier_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index('order_date');
            $table->index('supplier_invoice_number');
        });

        /*
        |--------------------------------------------------------------------------
        | purchase_order_items
        |--------------------------------------------------------------------------
        | تفاصيل المنتجات داخل أمر الشراء.
        */
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('purchase_order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->nullOnDelete();

            $table->string('product_name_ar', 255);
            $table->string('product_name_en', 255)->nullable();

            $table->string('sku', 120)->nullable();

            $table->string('color_name_ar', 120)->nullable();
            $table->string('color_name_en', 120)->nullable();
            $table->string('size', 120)->nullable();

            $table->unsignedInteger('quantity_ordered')->default(1);
            $table->unsignedInteger('quantity_received')->default(0);

            $table->decimal('unit_cost', 14, 3)->default(0);
            $table->decimal('discount_amount', 14, 3)->default(0);
            $table->decimal('tax_amount', 14, 3)->default(0);
            $table->decimal('line_total', 14, 3)->default(0);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('purchase_order_id');
            $table->index('product_id');
            $table->index('product_variant_id');
            $table->index('sku');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('suppliers');
    }
};

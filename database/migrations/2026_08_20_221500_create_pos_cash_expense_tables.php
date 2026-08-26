<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /*
         |--------------------------------------------------------------
         | cash_registers
         |--------------------------------------------------------------
         | خزائن / صناديق نقطة البيع.
         */
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->decimal('opening_balance', 12, 3)->default(0);
            $table->decimal('current_balance', 12, 3)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        /*
         |--------------------------------------------------------------
         | pos_sales
         |--------------------------------------------------------------
         | مبيعات نقطة البيع.
         */
        Schema::create('pos_sales', function (Blueprint $table) {
            $table->id();

            $table->string('sale_number', 100)->unique();

            $table->foreignId('cash_register_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('customer_name', 120)->nullable();
            $table->string('customer_phone', 30)->nullable();

            $table->decimal('subtotal', 12, 3)->default(0);
            $table->decimal('discount_amount', 12, 3)->default(0);
            $table->decimal('total', 12, 3)->default(0);

            $table->enum('payment_method', [
                'cash',
                'card',
                'transfer',
                'mixed',
            ])->default('cash');

            $table->enum('status', [
                'completed',
                'cancelled',
                'refunded',
            ])->default('completed');

            $table->string('notes', 1000)->nullable();

            $table->timestamps();

            $table->index('payment_method');
            $table->index('status');
            $table->index('created_at');
        });

        /*
         |--------------------------------------------------------------
         | pos_sale_items
         |--------------------------------------------------------------
         | تفاصيل المنتجات داخل كل عملية بيع.
         */
        Schema::create('pos_sale_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pos_sale_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('product_name_ar', 255);
            $table->string('product_name_en', 255)->nullable();
            $table->string('sku', 120)->nullable();

            $table->string('color_name_ar', 120)->nullable();
            $table->string('color_name_en', 120)->nullable();
            $table->string('size', 120)->nullable();

            $table->integer('quantity');
            $table->decimal('unit_price', 12, 3);
            $table->decimal('line_total', 12, 3);

            $table->timestamps();

            $table->index('product_variant_id');
        });

        /*
         |--------------------------------------------------------------
         | expenses
         |--------------------------------------------------------------
         | المصروفات الإدارية والتشغيلية.
         */
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cash_register_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('category', 120);
            $table->string('title', 180);
            $table->decimal('amount', 12, 3);

            $table->date('expense_date');

            $table->string('payment_method', 50)
                ->default('cash');

            $table->string('reference', 120)->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('expense_date');
            $table->index('category');
        });

        /*
         |--------------------------------------------------------------
         | cash_movements
         |--------------------------------------------------------------
         | سجل حركة الخزينة.
         */
        Schema::create('cash_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cash_register_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->enum('type', [
                'opening',
                'sale',
                'expense',
                'deposit',
                'withdrawal',
                'refund',
                'adjustment',
            ]);

            $table->decimal('amount', 12, 3);
            $table->decimal('balance_after', 12, 3);

            $table->string('reference', 120)->nullable();
            $table->string('description', 255)->nullable();

            $table->timestamps();

            $table->index(['cash_register_id', 'type']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_movements');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('pos_sale_items');
        Schema::dropIfExists('pos_sales');
        Schema::dropIfExists('cash_registers');
    }
};

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
        | تطوير بنود البيع
        |--------------------------------------------------------------------------
        | نحتفظ بسعر البيع الأصلي، الخصم على السطر، والكمية التي أُعيدت لاحقًا.
        */
        Schema::table('pos_sale_items', function (Blueprint $table) {
            $table->decimal('line_discount', 14, 3)
                ->default(0)
                ->after('unit_price');

            $table->unsignedInteger('returned_quantity')
                ->default(0)
                ->after('quantity');
        });

        /*
        |--------------------------------------------------------------------------
        | تطوير عمليات البيع
        |--------------------------------------------------------------------------
        | ربط عملية الاستبدال بفاتورة أصلية عند الحاجة.
        */
        Schema::table('pos_sales', function (Blueprint $table) {
            $table->foreignId('exchange_parent_sale_id')
                ->nullable()
                ->after('pos_shift_id')
                ->constrained('pos_sales')
                ->nullOnDelete();

            $table->string('receipt_number', 100)
                ->nullable()
                ->after('sale_number');

            $table->index('receipt_number');
        });

        /*
        |--------------------------------------------------------------------------
        | تطوير الدفعات
        |--------------------------------------------------------------------------
        | يسمح برد جزء من كل دفعة عند المرتجعات.
        */
        Schema::table('pos_sale_payments', function (Blueprint $table) {
            $table->decimal('refunded_amount', 14, 3)
                ->default(0)
                ->after('amount');
        });

        /*
        |--------------------------------------------------------------------------
        | pos_returns
        |--------------------------------------------------------------------------
        | رأس عملية المرتجع، سواء رد أموال أو جزء من عملية استبدال.
        */
        Schema::create('pos_returns', function (Blueprint $table) {
            $table->id();

            $table->string('return_number', 100)->unique();

            $table->foreignId('pos_sale_id')
                ->constrained('pos_sales')
                ->restrictOnDelete();

            $table->foreignId('cash_register_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('pos_shift_id')
                ->nullable()
                ->constrained('pos_shifts')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();

            $table->enum('return_type', [
                'refund',
                'exchange',
            ])->default('refund');

            $table->enum('refund_method', [
                'cash',
                'card',
                'bank_transfer',
                'store_credit',
                'original_method',
                'none',
            ])->default('original_method');

            $table->decimal('subtotal', 14, 3)->default(0);
            $table->decimal('deduction_amount', 14, 3)->default(0);
            $table->decimal('refund_amount', 14, 3)->default(0);

            $table->string('reason', 500)->nullable();
            $table->string('reference', 150)->nullable();
            $table->text('notes')->nullable();

            $table->timestamp('returned_at');
            $table->timestamps();

            $table->index('returned_at');
            $table->index('return_type');
            $table->index('refund_method');
        });

        Schema::create('pos_return_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pos_return_id')
                ->constrained('pos_returns')
                ->cascadeOnDelete();

            $table->foreignId('pos_sale_item_id')
                ->constrained('pos_sale_items')
                ->restrictOnDelete();

            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->nullOnDelete();

            $table->unsignedInteger('quantity');

            $table->decimal('unit_price', 14, 3);
            $table->decimal('line_discount', 14, 3)->default(0);
            $table->decimal('line_refund', 14, 3);

            $table->enum('stock_action', [
                'restock',
                'damaged',
                'no_stock_change',
            ])->default('restock');

            $table->string('reason', 500)->nullable();

            $table->timestamps();

            $table->index('product_variant_id');
        });

        /*
        |--------------------------------------------------------------------------
        | pos_shortcuts
        |--------------------------------------------------------------------------
        | اختصارات قابلة للتخصيص لاحقًا من إعدادات نقطة البيع.
        */
        Schema::create('pos_shortcuts', function (Blueprint $table) {
            $table->id();

            $table->string('action_key', 100)->unique();
            $table->string('shortcut', 80);
            $table->string('label_ar', 150);
            $table->string('label_en', 150)->nullable();

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index('is_active');
            $table->index('sort_order');
        });

        /*
        |--------------------------------------------------------------------------
        | pos_audit_logs
        |--------------------------------------------------------------------------
        | سجل مستقل للحركات الحساسة داخل الكاشير.
        */
        Schema::create('pos_audit_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('pos_shift_id')
                ->nullable()
                ->constrained('pos_shifts')
                ->nullOnDelete();

            $table->foreignId('pos_sale_id')
                ->nullable()
                ->constrained('pos_sales')
                ->nullOnDelete();

            $table->string('action', 120);
            $table->string('reference', 150)->nullable();
            $table->text('description')->nullable();

            $table->json('before_data')->nullable();
            $table->json('after_data')->nullable();

            $table->string('ip_address', 64)->nullable();
            $table->string('user_agent', 500)->nullable();

            $table->timestamps();

            $table->index('action');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_audit_logs');
        Schema::dropIfExists('pos_shortcuts');
        Schema::dropIfExists('pos_return_items');
        Schema::dropIfExists('pos_returns');

        Schema::table('pos_sale_payments', function (Blueprint $table) {
            $table->dropColumn('refunded_amount');
        });

        Schema::table('pos_sales', function (Blueprint $table) {
            $table->dropForeign(['exchange_parent_sale_id']);
            $table->dropIndex(['receipt_number']);

            $table->dropColumn([
                'exchange_parent_sale_id',
                'receipt_number',
            ]);
        });

        Schema::table('pos_sale_items', function (Blueprint $table) {
            $table->dropColumn([
                'line_discount',
                'returned_quantity',
            ]);
        });
    }
};

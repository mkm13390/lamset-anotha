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
        | pos_shifts
        |--------------------------------------------------------------------------
        | ورديات الكاشير: فتح / إغلاق / رصيد متوقع / رصيد فعلي.
        */
        Schema::create('pos_shifts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cash_register_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();

            $table->enum('status', [
                'open',
                'closed',
            ])->default('open');

            $table->decimal('opening_cash', 14, 3)->default(0);

            $table->decimal('cash_sales', 14, 3)->default(0);
            $table->decimal('cash_refunds', 14, 3)->default(0);
            $table->decimal('cash_in', 14, 3)->default(0);
            $table->decimal('cash_out', 14, 3)->default(0);

            $table->decimal('expected_cash', 14, 3)->default(0);
            $table->decimal('closing_cash', 14, 3)->nullable();
            $table->decimal('difference_amount', 14, 3)->nullable();

            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();

            $table->text('opening_notes')->nullable();
            $table->text('closing_notes')->nullable();

            $table->timestamps();

            $table->index(['cash_register_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('opened_at');
        });

        /*
        |--------------------------------------------------------------------------
        | تطوير pos_sales
        |--------------------------------------------------------------------------
        | ربط البيع بالعميل والوردية، وحفظ النقد المستلم والباقي.
        */
        Schema::table('pos_sales', function (Blueprint $table) {
            $table->foreignId('customer_id')
                ->nullable()
                ->after('user_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('pos_shift_id')
                ->nullable()
                ->after('cash_register_id')
                ->constrained('pos_shifts')
                ->nullOnDelete();

            $table->decimal('cash_received', 14, 3)
                ->default(0)
                ->after('total');

            $table->decimal('change_due', 14, 3)
                ->default(0)
                ->after('cash_received');

            $table->unsignedInteger('receipt_print_count')
                ->default(0)
                ->after('change_due');

            $table->timestamp('completed_at')
                ->nullable()
                ->after('status');

            $table->index('customer_id');
            $table->index('pos_shift_id');
        });

        /*
        |--------------------------------------------------------------------------
        | pos_sale_payments
        |--------------------------------------------------------------------------
        | يسمح بالدفع المختلط الحقيقي: نقدي + بطاقة + تحويل...
        */
        Schema::create('pos_sale_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pos_sale_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('cash_register_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('pos_shift_id')
                ->nullable()
                ->constrained('pos_shifts')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->enum('payment_method', [
                'cash',
                'card',
                'bank_transfer',
                'store_credit',
                'other',
            ]);

            $table->decimal('amount', 14, 3);

            $table->decimal('tendered_amount', 14, 3)
                ->nullable();

            $table->decimal('change_amount', 14, 3)
                ->default(0);

            $table->string('reference', 150)->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('payment_method');
            $table->index('created_at');
        });

        /*
        |--------------------------------------------------------------------------
        | pos_held_sales
        |--------------------------------------------------------------------------
        | تعليق الفاتورة دون خصم المخزون أو تسجيل دفعة.
        */
        Schema::create('pos_held_sales', function (Blueprint $table) {
            $table->id();

            $table->string('hold_number', 100)->unique();

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

            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('customer_name', 120)->nullable();
            $table->string('customer_phone', 30)->nullable();

            $table->decimal('subtotal', 14, 3)->default(0);
            $table->decimal('discount_amount', 14, 3)->default(0);
            $table->decimal('total', 14, 3)->default(0);

            $table->string('label', 150)->nullable();
            $table->text('notes')->nullable();

            $table->timestamp('held_at');
            $table->timestamps();

            $table->index('held_at');
            $table->index('user_id');
            $table->index('cash_register_id');
        });

        Schema::create('pos_held_sale_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pos_held_sale_id')
                ->constrained('pos_held_sales')
                ->cascadeOnDelete();

            $table->foreignId('product_variant_id')
                ->constrained('product_variants')
                ->restrictOnDelete();

            $table->unsignedInteger('quantity');

            $table->decimal('unit_price', 14, 3);
            $table->decimal('line_discount', 14, 3)->default(0);
            $table->decimal('line_total', 14, 3);

            $table->timestamps();

            $table->index('product_variant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_held_sale_items');
        Schema::dropIfExists('pos_held_sales');
        Schema::dropIfExists('pos_sale_payments');

        Schema::table('pos_sales', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['pos_shift_id']);

            $table->dropIndex(['customer_id']);
            $table->dropIndex(['pos_shift_id']);

            $table->dropColumn([
                'customer_id',
                'pos_shift_id',
                'cash_received',
                'change_due',
                'receipt_print_count',
                'completed_at',
            ]);
        });

        Schema::dropIfExists('pos_shifts');
    }
};

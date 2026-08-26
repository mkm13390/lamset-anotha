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
        | supplier_payments
        |--------------------------------------------------------------------------
        | الدفعات الفعلية التي يتم دفعها للمورد.
        */
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('supplier_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('purchase_order_id')
                ->nullable()
                ->constrained('purchase_orders')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('payment_number', 100)->unique();

            $table->date('payment_date');

            $table->decimal('amount', 14, 3);

            $table->enum('payment_method', [
                'cash',
                'card',
                'bank_transfer',
                'cheque',
                'other',
            ])->default('cash');

            $table->string('reference', 150)->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('supplier_id');
            $table->index('purchase_order_id');
            $table->index('payment_date');
            $table->index('payment_method');
        });

        /*
        |--------------------------------------------------------------------------
        | supplier_transactions
        |--------------------------------------------------------------------------
        | دفتر حركة المورد.
        |
        | debit  = مبلغ يزيد ما علينا للمورد
        | credit = مبلغ يخفض ما علينا للمورد
        |--------------------------------------------------------------------------
        */
        Schema::create('supplier_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('supplier_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('purchase_order_id')
                ->nullable()
                ->constrained('purchase_orders')
                ->nullOnDelete();

            $table->foreignId('supplier_payment_id')
                ->nullable()
                ->constrained('supplier_payments')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->enum('type', [
                'opening_balance',
                'purchase',
                'payment',
                'purchase_return',
                'adjustment_debit',
                'adjustment_credit',
            ]);

            $table->date('transaction_date');

            $table->decimal('debit', 14, 3)->default(0);
            $table->decimal('credit', 14, 3)->default(0);

            $table->decimal('balance_after', 14, 3)->default(0);

            $table->string('reference', 150)->nullable();
            $table->text('description')->nullable();

            $table->timestamps();

            $table->index('supplier_id');
            $table->index('purchase_order_id');
            $table->index('supplier_payment_id');
            $table->index('type');
            $table->index('transaction_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_transactions');
        Schema::dropIfExists('supplier_payments');
    }
};

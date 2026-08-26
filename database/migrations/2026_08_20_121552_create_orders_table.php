<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_number')->unique();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone', 30);
            $table->string('email')->nullable();

            $table->string('governorate');
            $table->string('wilayat');
            $table->text('address');
            $table->text('notes')->nullable();

            $table->string('delivery_method')->default('standard');
            $table->decimal('shipping_fee', 12, 3)->default(0);

            $table->string('payment_method')->default('cod');
            $table->string('payment_status')->default('pending');

            $table->string('status')->default('pending');

            $table->string('currency', 10)->default('OMR');

            $table->decimal('subtotal', 12, 3)->default(0);
            $table->decimal('discount_amount', 12, 3)->default(0);
            $table->string('coupon_code')->nullable();
            $table->decimal('total', 12, 3)->default(0);

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('payment_status');
            $table->index('payment_method');
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

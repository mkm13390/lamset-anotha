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
         | loyalty_accounts
         |--------------------------------------------------------------
         | حساب نقاط واحد لكل مستخدم.
         | نخزن الرصيد الحالي والإجمالي المكتسب والمستخدم.
         */
        Schema::create('loyalty_accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('points_balance')->default(0);
            $table->integer('total_points_earned')->default(0);
            $table->integer('total_points_redeemed')->default(0);

            $table->timestamps();
        });

        /*
         |--------------------------------------------------------------
         | loyalty_transactions
         |--------------------------------------------------------------
         | سجل كامل لكل حركة نقاط.
         |
         | earn    = إضافة نقاط
         | redeem  = استخدام نقاط
         | adjust  = تعديل يدوي لاحقًا من لوحة الإدارة
         | expire  = انتهاء صلاحية نقاط مستقبلًا
         */
        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('order_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->enum('type', [
                'earn',
                'redeem',
                'adjust',
                'expire',
            ]);

            /*
             | نخزن النقاط دائمًا كرقم موجب.
             | نوع الحركة هو الذي يحدد هل تضاف أو تخصم.
             */
            $table->integer('points');

            /*
             | قيمة الرصيد بعد تنفيذ هذه الحركة.
             | مفيد للتدقيق والتقارير لاحقًا.
             */
            $table->integer('balance_after');

            /*
             | مرجع اختياري مثل رقم الطلب أو سبب التعديل.
             */
            $table->string('reference', 120)->nullable();

            /*
             | وصف عربي/إداري للحركة.
             */
            $table->string('description', 255)->nullable();

            /*
             | صلاحية النقاط اختيارية.
             | نتركها الآن جاهزة للاستخدام مستقبلًا.
             */
            $table->dateTime('expires_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'type']);
            $table->index('order_id');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_transactions');
        Schema::dropIfExists('loyalty_accounts');
    }
};

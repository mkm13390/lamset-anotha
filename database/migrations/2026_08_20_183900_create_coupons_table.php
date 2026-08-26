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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();

            // الرمز الذي يدخله العميل، مثال: WELCOME10
            $table->string('code', 50)->unique();

            // اسم داخلي اختياري للكوبون يظهر في لوحة الإدارة
            $table->string('name', 120)->nullable();

            /*
             |--------------------------------------------------------------
             | discount_type
             |--------------------------------------------------------------
             | percentage = خصم بنسبة مئوية
             | fixed      = خصم بمبلغ ثابت
             */
            $table->enum('discount_type', ['percentage', 'fixed']);

            // قيمة الخصم:
            // percentage مثال: 10 = 10%
            // fixed مثال: 2.500 = 2.500 ر.ع
            $table->decimal('discount_value', 10, 3);

            // أقل قيمة للطلب حتى يصبح الكوبون صالحًا
            $table->decimal('minimum_order_amount', 10, 3)
                ->default(0);

            // بداية صلاحية الكوبون
            $table->dateTime('starts_at')->nullable();

            // نهاية صلاحية الكوبون
            $table->dateTime('ends_at')->nullable();

            // تفعيل أو تعطيل الكوبون بدون حذفه
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // فهارس تساعد لاحقًا في السلة والـ Checkout
            $table->index('is_active');
            $table->index('starts_at');
            $table->index('ends_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};

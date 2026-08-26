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
        Schema::table('users', function (Blueprint $table) {

            /*
             |----------------------------------------------------------
             | role
             |----------------------------------------------------------
             | customer = عميل عادي
             | staff    = موظف
             | admin    = مدير
             */
            $table->enum('role', [
                'customer',
                'staff',
                'admin',
            ])->default('customer')->after('phone');

            /*
             |----------------------------------------------------------
             | is_active
             |----------------------------------------------------------
             | يسمح بإيقاف حساب موظف أو مدير بدون حذفه.
             */
            $table->boolean('is_active')
                ->default(true)
                ->after('role');

            $table->index('role');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['is_active']);

            $table->dropColumn([
                'role',
                'is_active',
            ]);
        });
    }
};

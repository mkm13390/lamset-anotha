<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_variant_id')
                ->constrained('product_variants')
                ->cascadeOnDelete();

            $table->string('type', 50);

            /*
             * الكمية المستخدمة في الحركة نفسها.
             * تبقى موجبة، ونحدد اتجاهها من type.
             */
            $table->unsignedInteger('quantity');

            $table->integer('quantity_before');
            $table->integer('quantity_after');

            $table->decimal('unit_cost', 12, 3)->nullable();

            $table->string('reference')->nullable();

            $table->text('notes')->nullable();

            /*
             * مؤجل ربطه بمفتاح أجنبي للمستخدمين
             * حتى يكتمل نظام صلاحيات الإدارة.
             */
            $table->unsignedBigInteger('user_id')->nullable();

            $table->timestamps();

            $table->index('type');
            $table->index('created_at');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
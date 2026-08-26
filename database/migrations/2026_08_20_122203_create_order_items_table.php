<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
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

            $table->string('product_name_ar');
            $table->string('product_name_en')->nullable();

            $table->string('sku')->nullable();

            $table->string('color_name_ar')->nullable();
            $table->string('color_name_en')->nullable();
            $table->string('size')->nullable();

            $table->unsignedInteger('quantity')->default(1);

            $table->decimal('unit_price', 12, 3)->default(0);
            $table->decimal('line_total', 12, 3)->default(0);

            $table->string('image')->nullable();

            $table->timestamps();

            $table->index('order_id');
            $table->index('product_id');
            $table->index('product_variant_id');
            $table->index('sku');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

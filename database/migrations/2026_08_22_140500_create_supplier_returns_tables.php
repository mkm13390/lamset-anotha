<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_returns', function (Blueprint $table) {
            $table->id();

            $table->string('return_number', 100)->unique();

            $table->foreignId('supplier_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('purchase_order_id')
                ->nullable()
                ->constrained('purchase_orders')
                ->nullOnDelete();

            $table->foreignId('warehouse_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->date('return_date');

            $table->enum('status', [
                'draft',
                'completed',
                'cancelled',
            ])->default('completed');

            $table->decimal('total_amount', 14, 3)->default(0);

            $table->string('reference', 150)->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('supplier_id');
            $table->index('purchase_order_id');
            $table->index('warehouse_id');
            $table->index('return_date');
            $table->index('status');
        });

        Schema::create('supplier_return_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('supplier_return_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_variant_id')
                ->constrained('product_variants')
                ->restrictOnDelete();

            $table->unsignedInteger('quantity');
            $table->decimal('unit_cost', 14, 3)->default(0);
            $table->decimal('line_total', 14, 3)->default(0);

            $table->text('reason')->nullable();

            $table->timestamps();

            $table->index('supplier_return_id');
            $table->index('product_variant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_return_items');
        Schema::dropIfExists('supplier_returns');
    }
};

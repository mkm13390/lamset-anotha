<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | warehouses
        |--------------------------------------------------------------------------
        | مواقع / مخازن فعلية. نبدأ بمخزن رئيسي واحد، ويمكن لاحقًا إضافة فروع.
        */
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();

            $table->string('name', 150);
            $table->string('code', 50)->unique();

            $table->string('branch_name', 150)->nullable();
            $table->string('governorate', 100)->nullable();
            $table->string('wilayat', 100)->nullable();
            $table->text('address')->nullable();

            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('is_default');
            $table->index('is_active');
        });

        /*
        |--------------------------------------------------------------------------
        | product_variant_stocks
        |--------------------------------------------------------------------------
        | رصيد كل Variant داخل كل مخزن.
        */
        Schema::create('product_variant_stocks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('warehouse_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_variant_id')
                ->constrained('product_variants')
                ->cascadeOnDelete();

            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('reserved_quantity')->default(0);
            $table->unsignedInteger('damaged_quantity')->default(0);
            $table->unsignedInteger('sample_quantity')->default(0);

            $table->unsignedInteger('reorder_level')->default(0);
            $table->unsignedInteger('reorder_quantity')->default(0);

            $table->timestamps();

            $table->unique([
                'warehouse_id',
                'product_variant_id',
            ], 'variant_stocks_warehouse_variant_unique');

            $table->index('product_variant_id');
        });

        /*
        |--------------------------------------------------------------------------
        | stock_transfers
        |--------------------------------------------------------------------------
        | تحويلات المخزون بين المخازن.
        */
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();

            $table->string('transfer_number', 100)->unique();

            $table->foreignId('from_warehouse_id')
                ->constrained('warehouses')
                ->restrictOnDelete();

            $table->foreignId('to_warehouse_id')
                ->constrained('warehouses')
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->enum('status', [
                'draft',
                'in_transit',
                'completed',
                'cancelled',
            ])->default('draft');

            $table->date('transfer_date');
            $table->date('received_date')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('from_warehouse_id');
            $table->index('to_warehouse_id');
            $table->index('status');
            $table->index('transfer_date');
        });

        Schema::create('stock_transfer_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('stock_transfer_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_variant_id')
                ->constrained('product_variants')
                ->restrictOnDelete();

            $table->unsignedInteger('quantity');

            $table->timestamps();

            $table->index('stock_transfer_id');
            $table->index('product_variant_id');
        });

        /*
        |--------------------------------------------------------------------------
        | stock_counts
        |--------------------------------------------------------------------------
        | جلسات الجرد الشامل والجزئي.
        */
        Schema::create('stock_counts', function (Blueprint $table) {
            $table->id();

            $table->string('count_number', 100)->unique();

            $table->foreignId('warehouse_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->enum('type', [
                'full',
                'cycle',
            ])->default('full');

            $table->enum('status', [
                'draft',
                'counting',
                'completed',
                'cancelled',
            ])->default('draft');

            $table->date('count_date');

            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('warehouse_id');
            $table->index('status');
            $table->index('count_date');
        });

        Schema::create('stock_count_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('stock_count_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_variant_id')
                ->constrained('product_variants')
                ->restrictOnDelete();

            $table->unsignedInteger('system_quantity')->default(0);
            $table->unsignedInteger('counted_quantity')->nullable();

            $table->integer('difference')->default(0);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique([
                'stock_count_id',
                'product_variant_id',
            ], 'stock_count_variant_unique');

            $table->index('product_variant_id');
        });

        /*
        |--------------------------------------------------------------------------
        | إنشاء المخزن الرئيسي وترحيل الرصيد الحالي إليه
        |--------------------------------------------------------------------------
        | product_variants.stock_quantity يبقى موجودًا حاليًا حتى لا نكسر
        | السلة والـ POS والـ Checkout. سنجعله إجمالي الرصيد المتاح لاحقًا.
        */
        $warehouseId = DB::table('warehouses')->insertGetId([
            'name' => 'المخزن الرئيسي',
            'code' => 'MAIN',
            'is_default' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('product_variants')
            ->select('id', 'stock_quantity')
            ->orderBy('id')
            ->chunkById(500, function ($variants) use ($warehouseId) {
                $rows = [];

                foreach ($variants as $variant) {
                    $rows[] = [
                        'warehouse_id' => $warehouseId,
                        'product_variant_id' => $variant->id,
                        'quantity' => max(
                            (int) ($variant->stock_quantity ?? 0),
                            0
                        ),
                        'reserved_quantity' => 0,
                        'damaged_quantity' => 0,
                        'sample_quantity' => 0,
                        'reorder_level' => 0,
                        'reorder_quantity' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if ($rows) {
                    DB::table('product_variant_stocks')->insert($rows);
                }
            }, 'id');
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_count_items');
        Schema::dropIfExists('stock_counts');
        Schema::dropIfExists('stock_transfer_items');
        Schema::dropIfExists('stock_transfers');
        Schema::dropIfExists('product_variant_stocks');
        Schema::dropIfExists('warehouses');
    }
};

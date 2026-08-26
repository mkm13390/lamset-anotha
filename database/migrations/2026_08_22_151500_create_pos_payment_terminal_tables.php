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
        | payment_terminals
        |--------------------------------------------------------------------------
        | تعريف أجهزة الدفع البنكية وربطها بالخزينة.
        | الاتصال الفعلي يعتمد لاحقًا على البنك/المزود/موديل الجهاز.
        */
        Schema::create('payment_terminals', function (Blueprint $table) {
            $table->id();

            $table->string('name', 150);
            $table->string('provider', 150)->nullable();
            $table->string('bank_name', 150)->nullable();
            $table->string('terminal_id', 150)->nullable();

            $table->foreignId('cash_register_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->enum('integration_type', [
                'manual',
                'ecr',
                'lan',
                'usb_serial',
                'api',
                'app_to_app',
            ])->default('manual');

            $table->string('connection_host', 255)->nullable();
            $table->unsignedInteger('connection_port')->nullable();
            $table->string('device_identifier', 255)->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);

            $table->json('settings')->nullable();

            $table->timestamps();

            $table->index('provider');
            $table->index('bank_name');
            $table->index('terminal_id');
            $table->index('is_active');
        });

        /*
        |--------------------------------------------------------------------------
        | pos_terminal_transactions
        |--------------------------------------------------------------------------
        | كل طلب دفع يرسل إلى ماكينة البنك ونتيجته.
        */
        Schema::create('pos_terminal_transactions', function (Blueprint $table) {
            $table->id();

            $table->string('transaction_number', 120)->unique();

            $table->foreignId('payment_terminal_id')
                ->constrained('payment_terminals')
                ->restrictOnDelete();

            $table->foreignId('pos_sale_id')
                ->nullable()
                ->constrained('pos_sales')
                ->nullOnDelete();

            $table->foreignId('pos_sale_payment_id')
                ->nullable()
                ->constrained('pos_sale_payments')
                ->nullOnDelete();

            $table->foreignId('pos_shift_id')
                ->nullable()
                ->constrained('pos_shifts')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->enum('transaction_type', [
                'sale',
                'refund',
                'void',
                'preauth',
            ])->default('sale');

            $table->decimal('amount', 14, 3);

            $table->enum('status', [
                'pending',
                'sent',
                'approved',
                'declined',
                'cancelled',
                'timeout',
                'error',
            ])->default('pending');

            $table->string('approval_code', 150)->nullable();
            $table->string('bank_reference', 200)->nullable();
            $table->string('rrn', 150)->nullable();
            $table->string('masked_card', 50)->nullable();
            $table->string('card_scheme', 80)->nullable();

            $table->text('failure_reason')->nullable();

            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();

            $table->timestamp('sent_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('transaction_type');
            $table->index('bank_reference');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_terminal_transactions');
        Schema::dropIfExists('payment_terminals');
    }
};

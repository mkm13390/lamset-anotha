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
        | employee_profiles
        |--------------------------------------------------------------------------
        | الملف المالي للموظف. المستخدم يبقى في جدول users، وهنا فقط بيانات العمل.
        */
        Schema::create('employee_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->string('employee_code', 80)->unique();

            $table->string('job_title', 150)->nullable();
            $table->date('hire_date')->nullable();

            $table->decimal('basic_salary', 14, 3)->default(0);
            $table->decimal('fixed_allowance', 14, 3)->default(0);

            $table->enum('salary_type', [
                'monthly',
                'daily',
                'hourly',
            ])->default('monthly');

            $table->boolean('is_active')->default(true);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('is_active');
            $table->index('hire_date');
        });

        /*
        |--------------------------------------------------------------------------
        | employee_advances
        |--------------------------------------------------------------------------
        | السلف المالية للموظفين.
        */
        Schema::create('employee_advances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_profile_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->decimal('amount', 14, 3);

            $table->decimal('deducted_amount', 14, 3)->default(0);
            $table->decimal('balance_amount', 14, 3);

            $table->enum('status', [
                'active',
                'settled',
                'cancelled',
            ])->default('active');

            $table->date('advance_date');
            $table->date('first_deduction_date')->nullable();

            $table->string('reference', 150)->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('advance_date');
        });

        /*
        |--------------------------------------------------------------------------
        | employee_adjustments
        |--------------------------------------------------------------------------
        | مكافآت / خصومات / عمولات / إضافي، ويمكن ترحيلها لاحقًا لمسير راتب.
        */
        Schema::create('employee_adjustments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_profile_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('type', [
                'bonus',
                'deduction',
                'commission',
                'overtime',
                'allowance',
                'other_credit',
                'other_debit',
            ]);

            $table->decimal('amount', 14, 3);

            $table->date('effective_date');

            $table->boolean('is_recurring')->default(false);
            $table->boolean('is_processed')->default(false);

            $table->string('reference', 150)->nullable();
            $table->string('description', 500)->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('type');
            $table->index('effective_date');
            $table->index('is_processed');
        });

        /*
        |--------------------------------------------------------------------------
        | payroll_periods
        |--------------------------------------------------------------------------
        | فترة مسير الرواتب.
        */
        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();

            $table->string('period_name', 120);
            $table->date('start_date');
            $table->date('end_date');
            $table->date('payment_date')->nullable();

            $table->enum('status', [
                'draft',
                'calculated',
                'approved',
                'paid',
                'closed',
                'cancelled',
            ])->default('draft');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index(['start_date', 'end_date']);
        });

        /*
        |--------------------------------------------------------------------------
        | payroll_entries
        |--------------------------------------------------------------------------
        | راتب كل موظف داخل الفترة.
        */
        Schema::create('payroll_entries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payroll_period_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('employee_profile_id')
                ->constrained()
                ->restrictOnDelete();

            $table->decimal('basic_salary', 14, 3)->default(0);
            $table->decimal('allowances', 14, 3)->default(0);
            $table->decimal('bonuses', 14, 3)->default(0);
            $table->decimal('commissions', 14, 3)->default(0);
            $table->decimal('overtime', 14, 3)->default(0);

            $table->decimal('deductions', 14, 3)->default(0);
            $table->decimal('advance_deduction', 14, 3)->default(0);

            $table->decimal('gross_salary', 14, 3)->default(0);
            $table->decimal('net_salary', 14, 3)->default(0);

            $table->enum('status', [
                'draft',
                'approved',
                'paid',
                'cancelled',
            ])->default('draft');

            $table->timestamp('paid_at')->nullable();
            $table->string('payment_reference', 150)->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(
                ['payroll_period_id', 'employee_profile_id'],
                'payroll_period_employee_unique'
            );

            $table->index('status');
        });

        /*
        |--------------------------------------------------------------------------
        | financial_accounts
        |--------------------------------------------------------------------------
        | الخزن والحسابات البنكية والحسابات المحاسبية البسيطة.
        */
        Schema::create('financial_accounts', function (Blueprint $table) {
            $table->id();

            $table->string('name', 150);
            $table->string('code', 80)->unique();

            $table->enum('type', [
                'cash',
                'bank',
                'card_clearing',
                'income',
                'expense',
                'liability',
                'asset',
                'other',
            ]);

            $table->decimal('opening_balance', 14, 3)->default(0);
            $table->decimal('current_balance', 14, 3)->default(0);

            $table->string('currency', 3)->default('OMR');

            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('type');
            $table->index('is_active');
        });

        /*
        |--------------------------------------------------------------------------
        | finance_transactions
        |--------------------------------------------------------------------------
        | سجل مالي موحد للإيرادات والمصروفات والرواتب والتحويلات.
        */
        Schema::create('finance_transactions', function (Blueprint $table) {
            $table->id();

            $table->string('transaction_number', 120)->unique();

            $table->foreignId('financial_account_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('transaction_type', [
                'income',
                'expense',
                'salary',
                'advance',
                'advance_repayment',
                'transfer_in',
                'transfer_out',
                'adjustment_in',
                'adjustment_out',
            ]);

            $table->decimal('amount', 14, 3);

            $table->date('transaction_date');

            $table->string('category', 150)->nullable();
            $table->string('reference', 150)->nullable();
            $table->string('description', 500)->nullable();

            $table->nullableMorphs('source');

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('transaction_type');
            $table->index('transaction_date');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_transactions');
        Schema::dropIfExists('financial_accounts');
        Schema::dropIfExists('payroll_entries');
        Schema::dropIfExists('payroll_periods');
        Schema::dropIfExists('employee_adjustments');
        Schema::dropIfExists('employee_advances');
        Schema::dropIfExists('employee_profiles');
    }
};

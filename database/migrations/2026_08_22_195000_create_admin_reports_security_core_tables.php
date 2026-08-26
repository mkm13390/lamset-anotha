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
        | Permissions
        |--------------------------------------------------------------------------
        */
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 160);
            $table->string('code', 160)->unique();
            $table->string('group_name', 120)->nullable();
            $table->string('description', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('group_name');
            $table->index('is_active');
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role', 80);
            $table->foreignId('permission_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(
                ['role', 'permission_id'],
                'role_permission_unique'
            );
            $table->index('role');
        });

        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('permission_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('effect', [
                'allow',
                'deny',
            ])->default('allow');

            $table->foreignId('granted_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(
                ['user_id', 'permission_id'],
                'user_permission_unique'
            );

            $table->index('effect');
            $table->index('expires_at');
        });

        /*
        |--------------------------------------------------------------------------
        | Activity Logs
        |--------------------------------------------------------------------------
        */
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('action', 160);
            $table->string('module', 120)->nullable();

            $table->nullableMorphs('subject');

            $table->string('ip_address', 64)->nullable();
            $table->text('user_agent')->nullable();

            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamp('occurred_at')->useCurrent();
            $table->timestamps();

            $table->index('action');
            $table->index('module');
            $table->index('occurred_at');
            $table->index(['user_id', 'occurred_at']);
        });

        /*
        |--------------------------------------------------------------------------
        | Approval Workflows
        |--------------------------------------------------------------------------
        */
        Schema::create('approval_requests', function (Blueprint $table) {
            $table->id();

            $table->string('request_number', 120)->unique();

            $table->nullableMorphs('approvable');

            $table->string('approval_type', 120);

            $table->foreignId('requested_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'cancelled',
            ])->default('pending');

            $table->string('reason', 500)->nullable();
            $table->text('request_notes')->nullable();
            $table->text('review_notes')->nullable();

            $table->json('payload')->nullable();

            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            $table->index('approval_type');
            $table->index('status');
            $table->index('requested_at');
        });

        /*
        |--------------------------------------------------------------------------
        | Login / Session Audit
        |--------------------------------------------------------------------------
        */
        Schema::create('login_audits', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('email_or_identifier', 190)->nullable();

            $table->enum('event_type', [
                'login_success',
                'login_failed',
                'logout',
                'password_changed',
                'password_reset',
                'two_factor_enabled',
                'two_factor_disabled',
                'session_revoked',
            ]);

            $table->string('ip_address', 64)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_name', 180)->nullable();
            $table->string('location_label', 180)->nullable();

            $table->boolean('is_suspicious')->default(false);
            $table->json('metadata')->nullable();

            $table->timestamp('occurred_at')->useCurrent();
            $table->timestamps();

            $table->index('event_type');
            $table->index('is_suspicious');
            $table->index('occurred_at');
        });

        Schema::create('user_security_settings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->boolean('two_factor_enabled')->default(false);
            $table->string('two_factor_secret', 500)->nullable();
            $table->json('two_factor_recovery_codes')->nullable();

            $table->boolean('notify_new_login')->default(true);
            $table->boolean('notify_suspicious_login')->default(true);

            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->timestamp('last_security_review_at')->nullable();

            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | Feature Flags
        |--------------------------------------------------------------------------
        */
        Schema::create('feature_flags', function (Blueprint $table) {
            $table->id();

            $table->string('name', 160);
            $table->string('key', 160)->unique();

            $table->boolean('is_enabled')->default(false);

            $table->enum('scope', [
                'global',
                'role',
                'user',
                'branch',
            ])->default('global');

            $table->json('scope_values')->nullable();
            $table->json('config')->nullable();

            $table->string('description', 500)->nullable();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('is_enabled');
            $table->index('scope');
        });

        /*
        |--------------------------------------------------------------------------
        | System Health / Incidents
        |--------------------------------------------------------------------------
        */
        Schema::create('system_health_checks', function (Blueprint $table) {
            $table->id();

            $table->string('check_key', 160);
            $table->string('check_name', 180);

            $table->enum('status', [
                'healthy',
                'warning',
                'critical',
                'unknown',
            ])->default('unknown');

            $table->integer('response_time_ms')->nullable();
            $table->string('message', 500)->nullable();
            $table->json('details')->nullable();

            $table->timestamp('checked_at')->useCurrent();
            $table->timestamps();

            $table->index('check_key');
            $table->index('status');
            $table->index('checked_at');
        });

        Schema::create('system_incidents', function (Blueprint $table) {
            $table->id();

            $table->string('incident_number', 120)->unique();

            $table->enum('severity', [
                'info',
                'warning',
                'high',
                'critical',
            ])->default('warning');

            $table->enum('status', [
                'open',
                'investigating',
                'resolved',
                'ignored',
            ])->default('open');

            $table->string('title', 220);
            $table->text('description')->nullable();

            $table->string('source', 160)->nullable();
            $table->json('context')->nullable();

            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('resolved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('detected_at')->useCurrent();
            $table->timestamp('resolved_at')->nullable();

            $table->timestamps();

            $table->index('severity');
            $table->index('status');
            $table->index('detected_at');
        });

        /*
        |--------------------------------------------------------------------------
        | Backup Runs
        |--------------------------------------------------------------------------
        */
        Schema::create('backup_runs', function (Blueprint $table) {
            $table->id();

            $table->string('backup_number', 120)->unique();

            $table->enum('backup_type', [
                'database',
                'files',
                'full',
            ])->default('database');

            $table->enum('status', [
                'queued',
                'running',
                'completed',
                'failed',
                'cancelled',
            ])->default('queued');

            $table->string('storage_disk', 120)->nullable();
            $table->string('file_path', 500)->nullable();

            $table->unsignedBigInteger('size_bytes')->nullable();

            $table->string('checksum', 255)->nullable();
            $table->text('error_message')->nullable();

            $table->foreignId('requested_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->index('backup_type');
            $table->index('status');
            $table->index('created_at');
        });

        /*
        |--------------------------------------------------------------------------
        | Saved Reports / Scheduled Reports
        |--------------------------------------------------------------------------
        */
        Schema::create('saved_reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('name', 180);
            $table->string('report_key', 160);

            $table->json('filters')->nullable();
            $table->json('columns')->nullable();

            $table->enum('visibility', [
                'private',
                'role',
                'public',
            ])->default('private');

            $table->string('role_scope', 80)->nullable();

            $table->boolean('is_favorite')->default(false);

            $table->timestamps();

            $table->index('report_key');
            $table->index('visibility');
            $table->index('is_favorite');
        });

        Schema::create('scheduled_reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('saved_report_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('frequency', 80);
            $table->string('delivery_channel', 80)->default('email');

            $table->json('recipients')->nullable();
            $table->string('timezone', 80)->default('Asia/Muscat');

            $table->boolean('is_active')->default(true);

            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable();

            $table->timestamps();

            $table->index('is_active');
            $table->index('next_run_at');
        });

        /*
        |--------------------------------------------------------------------------
        | Admin Search Index
        |--------------------------------------------------------------------------
        */
        Schema::create('admin_search_index', function (Blueprint $table) {
            $table->id();

            $table->string('entity_type', 120);
            $table->unsignedBigInteger('entity_id');

            $table->string('title', 255);
            $table->string('subtitle', 500)->nullable();

            $table->longText('searchable_text')->nullable();

            $table->string('route_name', 180)->nullable();
            $table->json('route_parameters')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->index('is_active');
        });

        /*
        |--------------------------------------------------------------------------
        | Default permissions
        |--------------------------------------------------------------------------
        */
        $permissions = [
            ['name' => 'عرض لوحة الإدارة', 'code' => 'admin.dashboard.view', 'group_name' => 'admin'],
            ['name' => 'إدارة العملاء', 'code' => 'customers.manage', 'group_name' => 'customers'],
            ['name' => 'إدارة الموظفين', 'code' => 'employees.manage', 'group_name' => 'employees'],
            ['name' => 'إدارة المخزون', 'code' => 'inventory.manage', 'group_name' => 'inventory'],
            ['name' => 'إدارة المشتريات', 'code' => 'purchases.manage', 'group_name' => 'purchases'],
            ['name' => 'إدارة نقطة البيع', 'code' => 'pos.manage', 'group_name' => 'pos'],
            ['name' => 'إدارة المالية', 'code' => 'finance.manage', 'group_name' => 'finance'],
            ['name' => 'إدارة التسويق', 'code' => 'marketing.manage', 'group_name' => 'marketing'],
            ['name' => 'إدارة الشحن', 'code' => 'shipping.manage', 'group_name' => 'shipping'],
            ['name' => 'إدارة المدفوعات', 'code' => 'payments.manage', 'group_name' => 'payments'],
            ['name' => 'إدارة المرتجعات', 'code' => 'returns.manage', 'group_name' => 'returns'],
            ['name' => 'عرض التقارير', 'code' => 'reports.view', 'group_name' => 'reports'],
            ['name' => 'إدارة التقارير', 'code' => 'reports.manage', 'group_name' => 'reports'],
            ['name' => 'إدارة الصلاحيات', 'code' => 'permissions.manage', 'group_name' => 'security'],
            ['name' => 'عرض سجل النشاط', 'code' => 'activity.view', 'group_name' => 'security'],
            ['name' => 'إدارة الموافقات', 'code' => 'approvals.manage', 'group_name' => 'security'],
            ['name' => 'إدارة النسخ الاحتياطية', 'code' => 'backups.manage', 'group_name' => 'security'],
            ['name' => 'عرض صحة النظام', 'code' => 'health.view', 'group_name' => 'security'],
            ['name' => 'إدارة Feature Flags', 'code' => 'features.manage', 'group_name' => 'security'],
            ['name' => 'عرض سجلات الدخول', 'code' => 'login_audits.view', 'group_name' => 'security'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert([
                ...$permission,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $adminPermissionIds = DB::table('permissions')
            ->pluck('id')
            ->all();

        foreach ($adminPermissionIds as $permissionId) {
            DB::table('role_permissions')->insert([
                'role' => 'admin',
                'permission_id' => $permissionId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $staffCodes = [
            'admin.dashboard.view',
            'customers.manage',
            'inventory.manage',
            'purchases.manage',
            'pos.manage',
            'shipping.manage',
            'returns.manage',
            'reports.view',
        ];

        $staffPermissionIds = DB::table('permissions')
            ->whereIn('code', $staffCodes)
            ->pluck('id')
            ->all();

        foreach ($staffPermissionIds as $permissionId) {
            DB::table('role_permissions')->insert([
                'role' => 'staff',
                'permission_id' => $permissionId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Default feature flags
        |--------------------------------------------------------------------------
        */
        $flags = [
            [
                'name' => 'AI Marketing',
                'key' => 'ai_marketing',
                'is_enabled' => false,
                'description' => 'تشغيل ميزات الذكاء الاصطناعي التسويقية.',
            ],
            [
                'name' => 'Advanced Reports',
                'key' => 'advanced_reports',
                'is_enabled' => true,
                'description' => 'التقارير الإدارية المتقدمة.',
            ],
            [
                'name' => 'Customer Returns',
                'key' => 'customer_returns',
                'is_enabled' => true,
                'description' => 'طلبات الإرجاع من حساب العميل.',
            ],
            [
                'name' => 'Gift Cards',
                'key' => 'gift_cards',
                'is_enabled' => true,
                'description' => 'بطاقات الهدايا.',
            ],
            [
                'name' => 'Store Credit',
                'key' => 'store_credit',
                'is_enabled' => true,
                'description' => 'رصيد المتجر.',
            ],
        ];

        foreach ($flags as $flag) {
            DB::table('feature_flags')->insert([
                ...$flag,
                'scope' => 'global',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_search_index');
        Schema::dropIfExists('scheduled_reports');
        Schema::dropIfExists('saved_reports');
        Schema::dropIfExists('backup_runs');
        Schema::dropIfExists('system_incidents');
        Schema::dropIfExists('system_health_checks');
        Schema::dropIfExists('feature_flags');
        Schema::dropIfExists('user_security_settings');
        Schema::dropIfExists('login_audits');
        Schema::dropIfExists('approval_requests');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('user_permissions');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
    }
};

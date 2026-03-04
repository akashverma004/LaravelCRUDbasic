<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (! Schema::hasColumn('tenants', 'slug')) {
                $table->string('slug', 120)->nullable()->after('code');
                $table->unique('slug', 'tenants_slug_unique');
            }
            if (! Schema::hasColumn('tenants', 'email')) {
                $table->string('email')->nullable()->after('slug');
            }
            if (! Schema::hasColumn('tenants', 'phone')) {
                $table->string('phone', 30)->nullable()->after('email');
            }
            if (! Schema::hasColumn('tenants', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
            if (! Schema::hasColumn('tenants', 'country')) {
                $table->string('country', 3)->nullable()->after('address');
            }
            if (! Schema::hasColumn('tenants', 'timezone')) {
                $table->string('timezone', 64)->default('Asia/Kolkata')->after('country');
            }
            if (! Schema::hasColumn('tenants', 'currency')) {
                $table->string('currency', 8)->default('INR')->after('timezone');
            }
            if (! Schema::hasColumn('tenants', 'setup_completed')) {
                $table->boolean('setup_completed')->default(false)->after('is_active');
            }
            if (! Schema::hasColumn('tenants', 'setup_completed_at')) {
                $table->timestamp('setup_completed_at')->nullable()->after('setup_completed');
            }
            if (! Schema::hasColumn('tenants', 'owner_user_id')) {
                $table->unsignedBigInteger('owner_user_id')->nullable()->after('setup_completed_at');
                $table->index('owner_user_id', 'tenants_owner_user_id_idx');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'is_platform_admin')) {
                $table->boolean('is_platform_admin')->default(false)->after('tenant_id');
                $table->index('is_platform_admin', 'users_platform_admin_idx');
            }
        });

        // Existing tenants are assumed already operational.
        if (Schema::hasColumn('tenants', 'setup_completed')) {
            DB::table('tenants')
                ->where('setup_completed', false)
                ->update([
                    'setup_completed' => true,
                    'setup_completed_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_platform_admin')) {
                $table->dropIndex('users_platform_admin_idx');
                $table->dropColumn('is_platform_admin');
            }
        });

        Schema::table('tenants', function (Blueprint $table) {
            if (Schema::hasColumn('tenants', 'owner_user_id')) {
                $table->dropIndex('tenants_owner_user_id_idx');
                $table->dropColumn('owner_user_id');
            }
            if (Schema::hasColumn('tenants', 'setup_completed_at')) {
                $table->dropColumn('setup_completed_at');
            }
            if (Schema::hasColumn('tenants', 'setup_completed')) {
                $table->dropColumn('setup_completed');
            }
            if (Schema::hasColumn('tenants', 'currency')) {
                $table->dropColumn('currency');
            }
            if (Schema::hasColumn('tenants', 'timezone')) {
                $table->dropColumn('timezone');
            }
            if (Schema::hasColumn('tenants', 'country')) {
                $table->dropColumn('country');
            }
            if (Schema::hasColumn('tenants', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('tenants', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('tenants', 'email')) {
                $table->dropColumn('email');
            }
            if (Schema::hasColumn('tenants', 'slug')) {
                $table->dropUnique('tenants_slug_unique');
                $table->dropColumn('slug');
            }
        });
    }
};

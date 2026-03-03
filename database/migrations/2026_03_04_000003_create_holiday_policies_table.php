<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holiday_policies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->string('name');
            $table->string('code', 80)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->string('country_code', 3)->nullable();
            $table->string('state_code', 20)->nullable();
            $table->json('weekend_days')->nullable();
            $table->json('rules')->nullable();
            $table->json('exceptions')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'is_active']);
            $table->unique(['tenant_id', 'code']);
        });

        Schema::create('holiday_policy_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('holiday_policy_id')->constrained('holiday_policies')->cascadeOnDelete();
            $table->string('name');
            $table->date('holiday_date');
            $table->boolean('is_optional')->default(false);
            $table->json('rules')->nullable();
            $table->timestamps();

            $table->unique(['holiday_policy_id', 'holiday_date', 'name'], 'holiday_policy_dates_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holiday_policy_dates');
        Schema::dropIfExists('holiday_policies');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('overtime_policies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->string('name');
            $table->string('code', 80)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->unsignedSmallInteger('minimum_minutes')->default(30);
            $table->decimal('weekday_multiplier', 5, 2)->default(1.50);
            $table->decimal('weekend_multiplier', 5, 2)->default(2.00);
            $table->decimal('holiday_multiplier', 5, 2)->default(2.50);
            $table->decimal('max_hours_per_month', 6, 2)->nullable();
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
    }

    public function down(): void
    {
        Schema::dropIfExists('overtime_policies');
    }
};

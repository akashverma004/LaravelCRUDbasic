<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Goals / OKRs
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('progress')->default(0); // 0-100%
            $table->string('status')->default('active'); // active, completed, archived
            $table->date('due_date')->nullable();
            $table->string('priority')->default('medium'); // low, medium, high
            $table->timestamps();

            $table->index(['tenant_id', 'employee_id']);
        });

        // 2. Performance Reviews
        Schema::create('performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('reviewer_id'); // User ID
            $table->unsignedBigInteger('employee_id'); // Employee ID
            $table->string('review_cycle'); // e.g., "Q1 2024"
            $table->integer('rating')->nullable(); // 1-5
            $table->text('feedback')->nullable();
            $table->text('strengths')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->string('status')->default('draft'); // draft, submitted
            $table->timestamps();

            $table->index(['tenant_id', 'employee_id']);
            $table->index('review_cycle');
        });

        // 3. 1-on-1 Notes
        Schema::create('one_on_one_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('manager_id'); // User ID
            $table->unsignedBigInteger('employee_id'); // Employee ID
            $table->date('meeting_date');
            $table->text('talking_points')->nullable();
            $table->text('action_items')->nullable();
            $table->text('private_notes')->nullable(); // Only visible to manager
            $table->timestamps();

            $table->index(['tenant_id', 'employee_id']);
            $table->index('meeting_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('one_on_one_notes');
        Schema::dropIfExists('performance_reviews');
        Schema::dropIfExists('goals');
    }
};

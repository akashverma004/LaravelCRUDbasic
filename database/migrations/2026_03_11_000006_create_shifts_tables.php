<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Shift Templates
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('name'); // e.g., "Morning Shift"
            $table->time('start_time');
            $table->time('end_time');
            $table->string('color')->default('#0ea5e9'); // Tailwind-blue-500 equivalent
            $table->timestamps();

            $table->index('tenant_id');
        });

        // 2. Daily Shift Schedules
        Schema::create('shift_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('shift_id');
            $table->date('date');
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'date']);
            $table->index('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_schedules');
        Schema::dropIfExists('shifts');
    }
};

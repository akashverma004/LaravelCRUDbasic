<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pay Structures (Settings for each employee)
        Schema::create('pay_structures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('employee_id')->unique();
            $table->decimal('base_salary', 15, 2)->default(0);
            $table->json('allowances')->nullable(); // [{"name": "HRA", "amount": 500}, ...]
            $table->json('deductions')->nullable(); // [{"name": "Tax", "amount": 100}, ...]
            $table->string('currency')->default('USD');
            $table->timestamps();

            $table->index('tenant_id');
        });

        // 2. Payslips (Historical records)
        Schema::create('payslips', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('month'); // "March 2026"
            $table->date('period_start');
            $table->date('period_end');
            
            $table->decimal('base_salary', 15, 2);
            $table->decimal('total_allowances', 15, 2)->default(0);
            $table->decimal('total_deductions', 15, 2)->default(0);
            $table->decimal('net_pay', 15, 2);
            
            $table->string('status')->default('draft'); // draft, paid
            $table->json('details')->nullable(); // Snapshot of allowances/deductions
            $table->timestamps();

            $table->index(['tenant_id', 'employee_id']);
            $table->index('month');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payslips');
        Schema::dropIfExists('pay_structures');
    }
};

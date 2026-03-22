<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_adjustments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('workflow_request_id')->nullable(); // source reimbursement
            $table->string('label');                  // e.g. "Travel Reimbursement"
            $table->string('type')->default('addition'); // addition | deduction
            $table->decimal('amount', 15, 2);
            $table->string('status')->default('pending'); // pending | included | cancelled
            $table->string('month')->nullable();          // Set when included in a payroll run
            $table->timestamps();

            $table->index(['tenant_id', 'employee_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_adjustments');
    }
};

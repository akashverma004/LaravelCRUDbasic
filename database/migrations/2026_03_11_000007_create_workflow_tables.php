<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('requester_user_id');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->string('type', 50);
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('status', 30)->default('pending');
            $table->json('details')->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamp('resolved_at')->nullable();
            $table->unsignedBigInteger('last_action_by')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'type']);
            $table->index(['tenant_id', 'requester_user_id']);
            $table->index(['tenant_id', 'employee_id']);
        });

        Schema::create('workflow_approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workflow_request_id');
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('approver_user_id');
            $table->string('decision', 30)->default('pending');
            $table->text('comment')->nullable();
            $table->timestamp('acted_at')->nullable();
            $table->timestamps();

            $table->index(['workflow_request_id', 'approver_user_id']);
            $table->index(['tenant_id', 'approver_user_id', 'decision']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_approvals');
        Schema::dropIfExists('workflow_requests');
    }
};

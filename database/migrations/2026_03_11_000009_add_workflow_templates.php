<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('type', 50);
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('default_title')->nullable();
            $table->text('default_description')->nullable();
            $table->json('approval_steps')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['tenant_id', 'type']);
            $table->index(['tenant_id', 'is_active']);
        });

        Schema::table('workflow_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('workflow_template_id')->nullable()->after('employee_id');
            $table->index(['tenant_id', 'workflow_template_id']);
        });
    }

    public function down(): void
    {
        Schema::table('workflow_requests', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'workflow_template_id']);
            $table->dropColumn('workflow_template_id');
        });

        Schema::dropIfExists('workflow_templates');
    }
};

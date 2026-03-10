<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('employee_id')->nullable(); // null = company-wide document
            $table->string('title');
            $table->string('category', 50)->default('general');
            // identity, contract, certificate, payslip, letter, policy, general
            $table->string('file_path');
            $table->string('file_name'); // original file name
            $table->unsignedBigInteger('file_size')->default(0); // bytes
            $table->string('mime_type', 100)->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable(); // user id
            $table->boolean('is_private')->default(false); // only visible to employee + HR
            $table->timestamps();

            $table->index(['tenant_id', 'employee_id']);
            $table->index(['tenant_id', 'category']);
            $table->index('expiry_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};

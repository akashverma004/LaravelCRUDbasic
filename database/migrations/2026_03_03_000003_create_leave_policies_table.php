<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_policies', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('annual_limit')->default(12);
            $table->unsignedInteger('sick_limit')->default(8);
            $table->unsignedInteger('casual_limit')->default(6);
            $table->unsignedInteger('unpaid_limit')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_policies');
    }
};

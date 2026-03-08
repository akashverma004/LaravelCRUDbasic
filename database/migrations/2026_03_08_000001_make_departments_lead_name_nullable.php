<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Make departments.lead_name nullable — departments can exist without a lead,
     * especially during initial onboarding when no employees have been added yet.
     */
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->string('lead_name')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            // Restore to NOT NULL (set empty string as default to avoid data issues)
            $table->string('lead_name')->nullable(false)->default('')->change();
        });
    }
};

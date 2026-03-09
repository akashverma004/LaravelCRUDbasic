<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Additional columns on employees table ────────────────────────
        Schema::table('employees', function (Blueprint $table) {
            // Personal Details
            $table->date('date_of_birth')->nullable()->after('profile_photo');
            $table->string('gender', 20)->nullable()->after('date_of_birth');
            $table->string('marital_status', 20)->nullable()->after('gender');
            $table->string('blood_group', 10)->nullable()->after('marital_status');
            $table->string('nationality', 60)->nullable()->after('blood_group');
            $table->string('personal_email')->nullable()->after('nationality');

            // Emergency Contact
            $table->string('emergency_contact_name', 100)->nullable()->after('health_issues');
            $table->string('emergency_contact_phone', 20)->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_relationship', 50)->nullable()->after('emergency_contact_phone');

            // Identity Documents
            $table->string('pan_number', 20)->nullable()->after('emergency_contact_relationship');
            $table->string('aadhaar_number', 20)->nullable()->after('pan_number');
            $table->string('passport_number', 30)->nullable()->after('aadhaar_number');
            $table->date('passport_expiry')->nullable()->after('passport_number');

            // Bank Details
            $table->string('bank_name', 100)->nullable()->after('passport_expiry');
            $table->string('bank_account_number', 30)->nullable()->after('bank_name');
            $table->string('bank_ifsc', 20)->nullable()->after('bank_account_number');

            // Social / Bio
            $table->string('linkedin_url')->nullable()->after('bank_ifsc');
            $table->string('pronouns', 30)->nullable()->after('linkedin_url');
            $table->text('bio')->nullable()->after('pronouns');
        });

        // ── Employee Educations (one-to-many) ────────────────────────────
        Schema::create('employee_educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('tenant_id');
            $table->string('degree');
            $table->string('institution');
            $table->string('field_of_study')->nullable();
            $table->year('year_from')->nullable();
            $table->year('year_to')->nullable();
            $table->timestamps();

            $table->index('tenant_id');
        });

        // ── Employee Work Experiences (one-to-many) ──────────────────────
        Schema::create('employee_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('tenant_id');
            $table->string('company');
            $table->string('designation');
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('tenant_id');
        });

        // ── Employee Skills (one-to-many) ────────────────────────────────
        Schema::create('employee_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('tenant_id');
            $table->string('name');
            $table->string('proficiency', 30)->nullable(); // beginner, intermediate, expert
            $table->timestamps();

            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_skills');
        Schema::dropIfExists('employee_experiences');
        Schema::dropIfExists('employee_educations');

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_birth', 'gender', 'marital_status', 'blood_group',
                'nationality', 'personal_email',
                'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relationship',
                'pan_number', 'aadhaar_number', 'passport_number', 'passport_expiry',
                'bank_name', 'bank_account_number', 'bank_ifsc',
                'linkedin_url', 'pronouns', 'bio',
            ]);
        });
    }
};

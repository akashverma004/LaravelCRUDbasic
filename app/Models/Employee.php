<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Support\GeoLookup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'department_id',
        'manager_id',
        'role_id',
        'full_name',
        'profile_photo',
        'email',
        'phone',
        'job_title',
        'employment_type',
        'salary',
        'joined_on',
        'status',
        'country',
        'state',
        'city',
        'address',
        'zip_code',
        'hobbies',
        'likes',
        'food_preference',
        'health_issues',
        // Personal Details
        'date_of_birth',
        'gender',
        'marital_status',
        'blood_group',
        'nationality',
        'personal_email',
        // Emergency Contact
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        // Identity Documents
        'pan_number',
        'aadhaar_number',
        'passport_number',
        'passport_expiry',
        // Bank Details
        'bank_name',
        'bank_account_number',
        'bank_ifsc',
        // Social / Bio
        'linkedin_url',
        'pronouns',
        'bio',
    ];

    protected $casts = [
        'joined_on' => 'date',
        'date_of_birth' => 'date',
        'passport_expiry' => 'date',
        'salary' => 'decimal:2',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function leavePolicy(): HasOne
    {
        return $this->hasOne(EmployeeLeavePolicy::class);
    }

    public function educations(): HasMany
    {
        return $this->hasMany(EmployeeEducation::class);
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(EmployeeExperience::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(EmployeeSkill::class);
    }

    public function setCountryAttribute(?string $value): void
    {
        $this->attributes['country'] = GeoLookup::normalizeCountryCode($value);
    }

    public function setStateAttribute(?string $value): void
    {
        $this->attributes['state'] = GeoLookup::normalizeIndianStateCode($value);
    }
}

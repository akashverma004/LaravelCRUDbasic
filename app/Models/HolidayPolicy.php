<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasPolicyRules;
use App\Support\GeoLookup;
use App\Support\PolicyType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class HolidayPolicy extends Model
{
    use HasFactory, HasPolicyRules, BelongsToTenant, SoftDeletes;

    public const POLICY_TYPE = PolicyType::HOLIDAY;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'description',
        'is_active',
        'effective_from',
        'effective_to',
        'country_code',
        'state_code',
        'weekend_days',
        'rules',
        'exceptions',
        'metadata',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'weekend_days' => 'array',
    ];

    public function holidayDates(): HasMany
    {
        return $this->hasMany(HolidayPolicyDate::class);
    }

    public function setCountryCodeAttribute(?string $value): void
    {
        $this->attributes['country_code'] = GeoLookup::normalizeCountryCode($value);
    }

    public function setStateCodeAttribute(?string $value): void
    {
        $this->attributes['state_code'] = GeoLookup::normalizeIndianStateCode($value);
    }
}

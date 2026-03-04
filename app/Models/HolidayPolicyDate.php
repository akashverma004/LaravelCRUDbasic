<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HolidayPolicyDate extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'holiday_policy_id',
        'name',
        'holiday_date',
        'is_optional',
        'rules',
    ];

    protected $casts = [
        'holiday_date' => 'date',
        'is_optional' => 'boolean',
        'rules' => 'array',
    ];

    public function holidayPolicy(): BelongsTo
    {
        return $this->belongsTo(HolidayPolicy::class);
    }
}

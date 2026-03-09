<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSkill extends Model
{
    use BelongsToTenant;

    protected $table = 'employee_skills';

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'name',
        'proficiency',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}

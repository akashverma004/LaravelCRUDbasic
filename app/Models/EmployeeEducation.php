<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeEducation extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $table = 'employee_educations';

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'degree',
        'institution',
        'field_of_study',
        'year_from',
        'year_to',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}

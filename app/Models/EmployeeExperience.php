<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeExperience extends Model
{
    use BelongsToTenant;

    protected $table = 'employee_experiences';

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'company',
        'designation',
        'from_date',
        'to_date',
        'description',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}

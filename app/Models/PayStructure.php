<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayStructure extends Model
{
    protected $fillable = ['tenant_id', 'employee_id', 'base_salary', 'allowances', 'deductions', 'currency'];

    protected $casts = [
        'allowances' => 'array',
        'deductions' => 'array',
        'base_salary' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payslip extends Model
{
    protected $fillable = [
        'tenant_id', 'employee_id', 'month', 'period_start', 'period_end',
        'base_salary', 'total_allowances', 'total_deductions', 'net_pay',
        'status', 'details'
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'details' => 'array',
        'base_salary' => 'decimal:2',
        'total_allowances' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}

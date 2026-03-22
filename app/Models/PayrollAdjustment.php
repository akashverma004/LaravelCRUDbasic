<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollAdjustment extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'workflow_request_id',
        'label',
        'type',
        'amount',
        'status',
        'month',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function workflowRequest(): BelongsTo
    {
        return $this->belongsTo(WorkflowRequest::class);
    }

    /** Scope: pending adjustments for a specific employee */
    public function scopePendingFor($query, int $employeeId): object
    {
        return $query->where('employee_id', $employeeId)->where('status', 'pending');
    }
}

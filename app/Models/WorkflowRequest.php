<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowRequest extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'requester_user_id',
        'employee_id',
        'workflow_template_id',
        'type',
        'title',
        'description',
        'amount',
        'status',
        'details',
        'attachment_path',
        'attachment_name',
        'attachment_size',
        'attachment_mime_type',
        'submitted_at',
        'resolved_at',
        'last_action_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'details' => 'array',
        'attachment_size' => 'integer',
        'submitted_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_user_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(WorkflowTemplate::class, 'workflow_template_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(WorkflowApproval::class);
    }

    public function lastActionUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_action_by');
    }

    public static function types(): array
    {
        return [
            'reimbursement' => 'Reimbursement',
            'asset-request' => 'Asset Request',
            'asset-return' => 'Asset Return',
            'asset-repair' => 'Asset Repair',
            'profile-change' => 'Profile Change',
            'salary-change' => 'Salary Change',
            'offboarding' => 'Offboarding',
            'general' => 'General Request',
        ];
    }
}

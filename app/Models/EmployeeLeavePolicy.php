<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeLeavePolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'annual_limit',
        'sick_limit',
        'casual_limit',
        'unpaid_limit',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}

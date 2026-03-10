<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OneOnOneNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'manager_id',
        'employee_id',
        'meeting_date',
        'talking_points',
        'action_items',
        'private_notes',
    ];

    protected $casts = [
        'meeting_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}

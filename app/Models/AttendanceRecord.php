<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'attendance_date',
        'clock_in_at',
        'clock_out_at',
        'work_mode',
        'intervals',
        'status',
        'total_work_seconds',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'clock_in_at' => 'datetime:H:i',
        'clock_out_at' => 'datetime:H:i',
        'intervals' => 'array',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getCompletedWorkSeconds(): int
    {
        $intervals = $this->intervals ?? [];
        $total = 0;
        foreach ($intervals as $interval) {
            if ($interval['type'] === 'work' && isset($interval['start']) && isset($interval['end'])) {
                $start = \Carbon\Carbon::parse($interval['start']);
                $end = \Carbon\Carbon::parse($interval['end']);
                $total += $start->diffInSeconds($end);
            }
        }
        return $total;
    }

    public function getTotalWorkedSeconds(): int
    {
        $total = $this->getCompletedWorkSeconds();
        
        if ($this->status === 'clocked_in') {
            $intervals = $this->intervals ?? [];
            $lastInterval = end($intervals);
            if ($lastInterval && $lastInterval['type'] === 'work' && !isset($lastInterval['end'])) {
                $total += \Carbon\Carbon::parse($lastInterval['start'])->diffInSeconds(now());
            }
        }
        
        return $total;
    }

    public function updateCalculatedSeconds(): void
    {
        $this->total_work_seconds = $this->getCompletedWorkSeconds();
        $this->save();
    }
}

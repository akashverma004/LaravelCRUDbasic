<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    protected $fillable = [
        'tenant_id', 'employee_id', 'name', 'serial_number', 'category', 'status', 'assigned_at', 'notes'
    ];

    protected $casts = [
        'assigned_at' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public static function categories(): array
    {
        return [
            'laptop' => 'Laptops & Computers',
            'peripheral' => 'Peripherals (Mouse, Keyboard, etc.)',
            'furniture' => 'Furniture',
            'keys' => 'Physical Keys',
            'licence' => 'Software Licences',
            'other' => 'Other Equipment',
        ];
    }

    public static function statuses(): array
    {
        return [
            'available' => 'Available',
            'assigned' => 'Assigned',
            'maintenance' => 'Maintenance',
            'damaged' => 'Damaged',
            'lost' => 'Lost',
            'retired' => 'Retired',
        ];
    }
}

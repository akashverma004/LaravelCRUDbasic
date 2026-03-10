<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'title',
        'category',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'expiry_date',
        'notes',
        'uploaded_by',
        'is_private',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'is_private' => 'boolean',
        'file_size' => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Check if this document has expired.
     */
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Check if this document expires within the given days.
     */
    public function expiresSoon(int $days = 30): bool
    {
        return $this->expiry_date
            && !$this->isExpired()
            && $this->expiry_date->diffInDays(now()) <= $days;
    }

    /**
     * Human-readable file size.
     */
    public function getReadableSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }

    public static function categories(): array
    {
        return [
            'identity' => 'Identity Document',
            'contract' => 'Contract / Agreement',
            'certificate' => 'Certificate',
            'payslip' => 'Payslip',
            'letter' => 'Letter',
            'policy' => 'Company Policy',
            'general' => 'General',
        ];
    }
}

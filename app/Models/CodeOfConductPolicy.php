<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasPolicyRules;
use App\Support\PolicyType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CodeOfConductPolicy extends Model
{
    use HasFactory, HasPolicyRules, BelongsToTenant, SoftDeletes;

    public const POLICY_TYPE = PolicyType::CODE_OF_CONDUCT;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'description',
        'is_active',
        'effective_from',
        'effective_to',
        'document_version',
        'acknowledgement_required',
        'policy_text',
        'breach_actions',
        'rules',
        'exceptions',
        'metadata',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'acknowledgement_required' => 'boolean',
        'breach_actions' => 'array',
    ];

    public function getPlainTextSnippetAttribute(): ?string
    {
        if (! $this->policy_text) {
            return null;
        }

        $plain = trim(strip_tags($this->policy_text));

        return mb_strlen($plain) > 180 ? mb_substr($plain, 0, 180) . '...' : $plain;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OnboardingTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['tenant_id', 'name', 'description'];

    public function tasks(): HasMany
    {
        return $this->hasMany(OnboardingTemplateTask::class, 'template_id')->orderBy('sort_order');
    }
}

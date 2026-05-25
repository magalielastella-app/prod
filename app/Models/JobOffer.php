<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobOffer extends Model
{
    protected $fillable = [
        'title',
        'department',
        'location',
        'contract_type',
        'description',
        'requirements',
        'salary_range',
        'status',
        'published_at',
        'archived_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function interviewReports(): HasMany
    {
        return $this->hasMany(InterviewReport::class);
    }

    public function interviewEvents(): HasMany
    {
        return $this->hasMany(InterviewEvent::class);
    }
}

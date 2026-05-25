<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'city',
        'status',
        'source',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function cvDocuments(): HasMany
    {
        return $this->hasMany(CvDocument::class);
    }

    public function analyses(): HasMany
    {
        return $this->hasMany(CvAnalysis::class);
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    public const STATUS_A_ANALYSER = 'a_analyser';
    public const STATUS_SELECTIONNE = 'selectionne';
    public const STATUS_REJETE = 'rejete';

    public const STATUSES = [
        self::STATUS_A_ANALYSER => 'À analyser',
        self::STATUS_SELECTIONNE => 'Sélectionné',
        self::STATUS_REJETE => 'Rejeté',
    ];

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'city',
        'status',
        'source',
        'notes',
        'campaign_id',
        'job_position_id',
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

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(RecruitmentCampaign::class, 'campaign_id');
    }

    public function jobPosition(): BelongsTo
    {
        return $this->belongsTo(JobPosition::class);
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

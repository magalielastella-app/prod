<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterviewReport extends Model
{
    protected $fillable = [
        'candidate_id',
        'job_offer_id',
        'interviewer_id',
        'interview_date',
        'rating',
        'strengths',
        'weaknesses',
        'notes',
        'recommendation',
    ];

    protected function casts(): array
    {
        return [
            'interview_date' => 'date',
            'rating' => 'integer',
        ];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function jobOffer(): BelongsTo
    {
        return $this->belongsTo(JobOffer::class);
    }

    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }
}

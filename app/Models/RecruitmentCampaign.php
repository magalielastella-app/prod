<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecruitmentCampaign extends Model
{
    protected $fillable = ['title', 'description', 'job_offer_id', 'status'];

    public function jobOffer(): BelongsTo
    {
        return $this->belongsTo(JobOffer::class);
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class, 'campaign_id');
    }
}

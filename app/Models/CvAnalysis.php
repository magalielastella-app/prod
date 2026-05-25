<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CvAnalysis extends Model
{
    protected $table = 'cv_analyses';

    protected $fillable = [
        'candidate_id',
        'cv_document_id',
        'analysis',
        'prompt_used',
        'model_used',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function cvDocument(): BelongsTo
    {
        return $this->belongsTo(CvDocument::class);
    }
}

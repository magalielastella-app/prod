<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterviewScript extends Model
{
    protected $fillable = [
        'title',
        'description',
        'sections',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'sections' => 'array',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

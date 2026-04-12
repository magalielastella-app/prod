<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'date', 'start', 'end', 'role'];

    protected $casts = [
        'date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Durée du créneau en heures (décimal).
     */
    public function getHoursAttribute(): float
    {
        [$sh, $sm] = explode(':', $this->start);
        [$eh, $em] = explode(':', $this->end);
        $mins = ((int) $eh * 60 + (int) $em) - ((int) $sh * 60 + (int) $sm);
        return round($mins / 60, 2);
    }
}

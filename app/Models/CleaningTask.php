<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CleaningTask extends Model
{
    use HasFactory;

    protected $fillable = ['zone', 'frequency', 'last_done', 'agent'];

    protected $casts = [
        'last_done' => 'date',
    ];

    public const FREQUENCIES = ['Quotidien', 'Hebdomadaire', 'Bi-mensuel', 'Mensuel', 'Trimestriel'];

    protected const FREQUENCY_DAYS = [
        'Quotidien' => 1,
        'Hebdomadaire' => 7,
        'Bi-mensuel' => 15,
        'Mensuel' => 30,
        'Trimestriel' => 90,
    ];

    /**
     * Statut de la tâche : à jour, à faire ou en retard.
     */
    public function getStatusAttribute(): array
    {
        if (!$this->last_done) {
            return ['label' => 'À faire', 'cls' => 'warn'];
        }
        $days = $this->last_done->diffInDays(now(), false);
        $limit = self::FREQUENCY_DAYS[$this->frequency] ?? 7;
        if ($days > $limit) return ['label' => 'En retard', 'cls' => 'danger'];
        if ($days >= $limit) return ['label' => 'À faire aujourd\'hui', 'cls' => 'warn'];
        return ['label' => 'À jour', 'cls' => 'ok'];
    }

    protected $appends = ['status'];
}

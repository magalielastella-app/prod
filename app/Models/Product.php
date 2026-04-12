<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'category', 'quantity', 'unit',
        'min_threshold', 'expiration', 'price', 'supplier',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'min_threshold' => 'decimal:2',
        'price' => 'decimal:2',
        'expiration' => 'date',
    ];

    /**
     * Statut calculé : OK / stock bas / rupture / bientôt périmé / périmé.
     */
    public function getStatusAttribute(): array
    {
        $today = now()->startOfDay();
        if ($this->expiration && $this->expiration->lt($today)) {
            return ['label' => 'Périmé', 'cls' => 'danger'];
        }
        if ($this->expiration && $this->expiration->diffInDays($today, false) >= -3) {
            return ['label' => 'Bientôt périmé', 'cls' => 'warn'];
        }
        if ($this->quantity <= 0) {
            return ['label' => 'Rupture', 'cls' => 'danger'];
        }
        if ($this->min_threshold !== null && $this->quantity <= $this->min_threshold) {
            return ['label' => 'Stock bas', 'cls' => 'warn'];
        }
        return ['label' => 'OK', 'cls' => 'ok'];
    }

    protected $appends = ['status'];
}

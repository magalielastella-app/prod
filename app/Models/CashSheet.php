<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashSheet extends Model
{
    use HasFactory;

    /** Colonnes modes de règlement, dans l'ordre d'affichage. */
    public const PAYMENT_FIELDS = [
        'ca_plateforme' => 'CA plateforme',
        'cb' => 'CB',
        'cb_sans_contact' => 'CB sans contact',
        'espece' => 'Espèces',
        'ticket_restaurant' => 'Ticket restaurant',
        'borne' => 'Borne',
    ];

    protected $fillable = [
        'date', 'ca', 'ca_plateforme', 'cb', 'cb_sans_contact',
        'espece', 'ticket_restaurant', 'borne', 'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'ca' => 'decimal:2',
        'ca_plateforme' => 'decimal:2',
        'cb' => 'decimal:2',
        'cb_sans_contact' => 'decimal:2',
        'espece' => 'decimal:2',
        'ticket_restaurant' => 'decimal:2',
        'borne' => 'decimal:2',
    ];

    /**
     * Total attendu (somme des modes de règlement), pour vérifier la cohérence
     * avec le CA saisi manuellement.
     */
    public function getPaymentTotalAttribute(): float
    {
        return (float) (
            $this->ca_plateforme + $this->cb + $this->cb_sans_contact
            + $this->espece + $this->ticket_restaurant + $this->borne
        );
    }

    public function getDifferenceAttribute(): float
    {
        return round((float) $this->ca - $this->payment_total, 2);
    }

    protected $appends = ['payment_total', 'difference'];
}

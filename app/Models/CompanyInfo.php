<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'legal_form', 'siret', 'vat_number', 'rcs', 'ape_code',
        'capital', 'address', 'postal_code', 'city', 'phone', 'email',
        'website', 'manager_name', 'opening_hours', 'notes',
    ];

    protected $casts = [
        'capital' => 'decimal:2',
    ];

    /** Renvoie l'unique ligne d'informations société (singleton). */
    public static function current(): self
    {
        return self::firstOrCreate([], ['name' => 'Smash You']);
    }
}

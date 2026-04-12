<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemperatureLog extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'zone', 'temp', 'time', 'compliant', 'agent'];

    protected $casts = [
        'date' => 'date',
        'temp' => 'decimal:1',
        'compliant' => 'boolean',
    ];

    /** Seuils HACCP par zone (en °C). */
    public const ZONE_LIMITS = [
        'Réfrigérateur' => ['max' => 4],
        'Vitrine froide' => ['max' => 4],
        'Chambre froide viandes' => ['max' => 2],
        'Chambre froide légumes' => ['max' => 8],
        'Congélateur' => ['max' => -18],
        'Chaud (maintien)' => ['min' => 63],
    ];

    public static function isCompliant(string $zone, float $temp): bool
    {
        $limit = self::ZONE_LIMITS[$zone] ?? null;
        if (!$limit) return true;
        if (isset($limit['max']) && $temp > $limit['max']) return false;
        if (isset($limit['min']) && $temp < $limit['min']) return false;
        return true;
    }
}

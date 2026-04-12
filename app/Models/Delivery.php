<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'supplier', 'product', 'quantity',
        'unit', 'temp_delivery', 'dlc', 'compliant',
    ];

    protected $casts = [
        'date' => 'date',
        'dlc' => 'date',
        'quantity' => 'decimal:2',
        'temp_delivery' => 'decimal:1',
        'compliant' => 'boolean',
    ];
}

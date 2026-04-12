<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory;

    public const CATEGORIES = [
        'Fiche technique',
        'Administratif',
        'Hygiène',
        'Contrat',
        'Facture',
        'Recette',
        'Autre',
    ];

    protected $fillable = [
        'title', 'category', 'description',
        'file_path', 'original_name', 'mime_type', 'size',
    ];

    public function getUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::disk('public')->url($this->file_path) : null;
    }

    public function getSizeHumanAttribute(): string
    {
        if (!$this->size) return '—';
        $units = ['o', 'Ko', 'Mo', 'Go'];
        $i = 0; $s = $this->size;
        while ($s >= 1024 && $i < count($units) - 1) { $s /= 1024; $i++; }
        return round($s, 1) . ' ' . $units[$i];
    }

    protected $appends = ['url', 'size_human'];
}

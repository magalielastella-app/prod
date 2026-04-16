<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewTemplateModel extends Model
{
    protected $table = 'review_templates';

    protected $fillable = ['key', 'label', 'header', 'sections'];

    protected function casts(): array
    {
        return [
            'header' => 'array',
            'sections' => 'array',
        ];
    }

    public function toDefinition(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'header' => $this->header ?? [],
            'sections' => $this->sections ?? [],
        ];
    }
}

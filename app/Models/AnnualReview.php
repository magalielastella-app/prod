<?php

namespace App\Models;

use App\Support\ReviewTemplate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnnualReview extends Model
{
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_EMPLOYEE_DRAFT = 'employee_draft';
    public const STATUS_READY_FOR_MANAGER = 'ready_for_manager';
    public const STATUS_MANAGER_DRAFT = 'manager_draft';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_SIGNED = 'signed';

    public const STATUSES = [
        self::STATUS_SCHEDULED => 'Planifié',
        self::STATUS_EMPLOYEE_DRAFT => 'En préparation (salarié)',
        self::STATUS_READY_FOR_MANAGER => 'À traiter par le manager',
        self::STATUS_MANAGER_DRAFT => 'En préparation (manager)',
        self::STATUS_COMPLETED => 'Prêt à signer',
        self::STATUS_SIGNED => 'Signé',
    ];

    protected $fillable = [
        'employee_id',
        'manager_id',
        'year',
        'scheduled_for',
        'status',
        'template_key',
        'header',
        'employee_answers',
        'manager_answers',
        'employee_signed_at',
        'manager_signed_at',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_for' => 'date',
            'header' => 'array',
            'employee_answers' => 'array',
            'manager_answers' => 'array',
            'employee_signed_at' => 'datetime',
            'manager_signed_at' => 'datetime',
            'year' => 'integer',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function isSigned(): bool
    {
        return $this->status === self::STATUS_SIGNED;
    }

    public function isEditableByEmployee(): bool
    {
        return in_array($this->status, [
            self::STATUS_SCHEDULED,
            self::STATUS_EMPLOYEE_DRAFT,
        ], true);
    }

    public function isEditableByManager(): bool
    {
        return in_array($this->status, [
            self::STATUS_READY_FOR_MANAGER,
            self::STATUS_MANAGER_DRAFT,
            self::STATUS_COMPLETED,
        ], true);
    }

    public function template(): array
    {
        return ReviewTemplate::get($this->template_key ?? ReviewTemplate::DEFAULT);
    }
}

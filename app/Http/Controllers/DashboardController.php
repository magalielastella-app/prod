<?php

namespace App\Http\Controllers;

use App\Models\AnnualReview;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        $currentYear = (int) Carbon::now()->year;

        $baseQuery = AnnualReview::query();
        if ($user->isAdmin()) {
            // all
        } elseif ($user->isManager()) {
            $baseQuery->where(function ($q) use ($user) {
                $q->where('manager_id', $user->id)
                  ->orWhere('co_manager_id', $user->id)
                  ->orWhere('employee_id', $user->id);
            });
        } else {
            $baseQuery->where('employee_id', $user->id);
        }

        $scoped = (clone $baseQuery);
        $stats = [
            'total' => (clone $scoped)->where('year', $currentYear)->count(),
            'to_prepare' => (clone $scoped)->whereIn('status', [
                AnnualReview::STATUS_SCHEDULED,
                AnnualReview::STATUS_EMPLOYEE_DRAFT,
            ])->count(),
            'to_review' => (clone $scoped)->whereIn('status', [
                AnnualReview::STATUS_READY_FOR_MANAGER,
                AnnualReview::STATUS_MANAGER_DRAFT,
            ])->count(),
            'to_sign' => (clone $scoped)->where('status', AnnualReview::STATUS_COMPLETED)->count(),
            'signed' => (clone $scoped)->where('status', AnnualReview::STATUS_SIGNED)
                ->where('year', $currentYear)->count(),
            'team' => $user->isAdmin() ? User::count() : null,
        ];

        $upcoming = (clone $baseQuery)
            ->with(['employee:id,name,position', 'manager:id,name'])
            ->whereIn('status', [
                AnnualReview::STATUS_SCHEDULED,
                AnnualReview::STATUS_EMPLOYEE_DRAFT,
                AnnualReview::STATUS_READY_FOR_MANAGER,
                AnnualReview::STATUS_MANAGER_DRAFT,
                AnnualReview::STATUS_COMPLETED,
            ])
            ->orderByRaw('scheduled_for IS NULL')
            ->orderBy('scheduled_for')
            ->limit(8)
            ->get()
            ->map(fn (AnnualReview $r) => [
                'id' => $r->id,
                'year' => $r->year,
                'status' => $r->status,
                'status_label' => $r->statusLabel(),
                'scheduled_for' => optional($r->scheduled_for)->toDateString(),
                'employee' => $r->employee ? [
                    'id' => $r->employee->id,
                    'name' => $r->employee->name,
                    'position' => $r->employee->position,
                ] : null,
                'manager' => $r->manager?->only(['id', 'name']),
            ]);

        // Mes actions à effectuer maintenant : entretiens où l'utilisateur
        // doit agir (préparer son auto-éval, traiter en tant que manager,
        // ou signer). Triées par urgence.
        $myActions = $this->buildMyActions($user);

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'upcoming' => $upcoming,
            'myActions' => $myActions,
            'currentYear' => $currentYear,
        ]);
    }

    /** @return list<array<string,mixed>> */
    private function buildMyActions(User $user): array
    {
        $actions = [];

        // 1) Mes propres entretiens (en tant que salarié)
        $ownReviews = AnnualReview::with('manager:id,name')
            ->where('employee_id', $user->id)
            ->orderByDesc('year')
            ->get();

        foreach ($ownReviews as $r) {
            $action = $this->buildOwnActionFromReview($r);
            if ($action) {
                $actions[] = $action;
            }
        }

        // 2) Entretiens que je conduis (en tant que manager)
        if ($user->isManager() || $user->isAdmin()) {
            $managedReviews = AnnualReview::with('employee:id,name,position')
                ->where('manager_id', $user->id)
                ->orderByDesc('year')
                ->get();

            foreach ($managedReviews as $r) {
                $action = $this->buildManagerActionFromReview($r);
                if ($action) {
                    $actions[] = $action;
                }
            }
        }

        // Tri : les actions urgentes (à préparer / à traiter / à signer) en premier
        usort($actions, fn ($a, $b) => ($b['priority'] ?? 0) <=> ($a['priority'] ?? 0));

        return $actions;
    }

    private function buildOwnActionFromReview(AnnualReview $r): ?array
    {
        $base = [
            'review_id' => $r->id,
            'year' => $r->year,
            'role' => 'employee',
            'manager_name' => $r->manager?->name,
        ];

        return match ($r->status) {
            AnnualReview::STATUS_SCHEDULED => array_merge($base, [
                'title' => "Préparer mon entretien {$r->year}",
                'subtitle' => 'Vous pouvez commencer votre auto-évaluation.',
                'cta' => 'Commencer la préparation',
                'tone' => 'primary',
                'priority' => 90,
            ]),
            AnnualReview::STATUS_EMPLOYEE_DRAFT => array_merge($base, [
                'title' => "Continuer mon entretien {$r->year}",
                'subtitle' => "Brouillon en cours — pensez à l'envoyer au manager quand c'est prêt.",
                'cta' => 'Reprendre la préparation',
                'tone' => 'primary',
                'priority' => 95,
            ]),
            AnnualReview::STATUS_READY_FOR_MANAGER, AnnualReview::STATUS_MANAGER_DRAFT => array_merge($base, [
                'title' => "Mon entretien {$r->year} est entre les mains du manager",
                'subtitle' => 'Vous pourrez signer dès qu\'il aura finalisé sa partie.',
                'cta' => 'Voir mon entretien',
                'tone' => 'info',
                'priority' => 30,
            ]),
            AnnualReview::STATUS_COMPLETED => $r->employee_signed_at
                ? null
                : array_merge($base, [
                    'title' => "Signer mon entretien {$r->year}",
                    'subtitle' => 'Le manager a finalisé — votre signature est attendue.',
                    'cta' => 'Signer maintenant',
                    'tone' => 'urgent',
                    'priority' => 100,
                ]),
            default => null,
        };
    }

    private function buildManagerActionFromReview(AnnualReview $r): ?array
    {
        $base = [
            'review_id' => $r->id,
            'year' => $r->year,
            'role' => 'manager',
            'employee_name' => $r->employee?->name,
            'employee_position' => $r->employee?->position,
        ];

        return match ($r->status) {
            AnnualReview::STATUS_READY_FOR_MANAGER => array_merge($base, [
                'title' => "Conduire l'entretien de {$r->employee?->name}",
                'subtitle' => "L'auto-évaluation est prête, à vous de compléter votre partie.",
                'cta' => 'Ouvrir et compléter',
                'tone' => 'primary',
                'priority' => 90,
            ]),
            AnnualReview::STATUS_MANAGER_DRAFT => array_merge($base, [
                'title' => "Continuer l'entretien de {$r->employee?->name}",
                'subtitle' => 'Brouillon en cours — pensez à finaliser pour signature.',
                'cta' => 'Reprendre',
                'tone' => 'primary',
                'priority' => 85,
            ]),
            AnnualReview::STATUS_COMPLETED => $r->manager_signed_at
                ? null
                : array_merge($base, [
                    'title' => "Signer l'entretien de {$r->employee?->name}",
                    'subtitle' => 'En attente de votre signature.',
                    'cta' => 'Signer maintenant',
                    'tone' => 'urgent',
                    'priority' => 100,
                ]),
            default => null,
        };
    }
}

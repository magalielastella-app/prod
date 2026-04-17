<?php

namespace App\Http\Controllers;

use App\Models\AnnualReview;
use App\Models\User;
use App\Support\ReviewTemplate;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class AnnualReviewController extends Controller
{
    /** Liste des entretiens que l'utilisateur peut voir. */
    public function index(Request $request): Response
    {
        $user = $request->user();

        $query = AnnualReview::with([
                'employee:id,name,email,position,department',
                'manager:id,name',
                'coManager:id,name',
            ])
            ->orderByDesc('year')
            ->orderBy('scheduled_for');

        if ($user->isAdmin()) {
            // tout
        } elseif ($user->isManager()) {
            $query->where(function ($q) use ($user) {
                $q->where('manager_id', $user->id)
                  ->orWhere('co_manager_id', $user->id)
                  ->orWhere('employee_id', $user->id);
            });
        } else {
            $query->where('employee_id', $user->id);
        }

        $reviews = $query->get()->map(fn (AnnualReview $r) => [
            'id' => $r->id,
            'year' => $r->year,
            'status' => $r->status,
            'status_label' => $r->statusLabel(),
            'scheduled_for' => optional($r->scheduled_for)->toDateString(),
            'employee' => $r->employee ? [
                'id' => $r->employee->id,
                'name' => $r->employee->name,
                'position' => $r->employee->position,
                'department' => $r->employee->department,
            ] : null,
            'manager' => $r->manager ? [
                'id' => $r->manager->id,
                'name' => $r->manager->name,
            ] : null,
            'co_manager' => $r->coManager ? [
                'id' => $r->coManager->id,
                'name' => $r->coManager->name,
            ] : null,
            'signed' => $r->isSigned(),
            'is_mine' => $r->employee_id === $user->id,
        ]);

        // Seule la directrice (admin) peut planifier des entretiens.
        $employees = [];
        $potentialManagers = [];
        if ($user->isAdmin()) {
            $employees = User::query()
                ->orderBy('position')
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'position', 'department']);

            $potentialManagers = User::query()
                ->whereIn('role', [User::ROLE_MANAGER, User::ROLE_ADMIN])
                ->orderBy('name')
                ->get(['id', 'name', 'position']);
        }

        return Inertia::render('Reviews/Index', [
            'reviews' => $reviews,
            'employees' => $employees,
            'potentialManagers' => $potentialManagers,
            'defaultYear' => (int) Carbon::now()->year,
            'can' => [
                'create' => $user->isAdmin(),
                'pickManager' => $user->isAdmin(),
                'delete' => $user->isAdmin(),
            ],
        ]);
    }

    /**
     * Planifie un ou plusieurs entretiens. Accepte une liste d'assignations :
     *   year, assignments: [{ employee_id, manager_id?, scheduled_for? }]
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', AnnualReview::class);

        $data = $request->validate([
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'assignments' => ['required', 'array', 'min:1'],
            'assignments.*.employee_id' => ['required', 'exists:users,id'],
            'assignments.*.manager_id' => ['nullable', 'exists:users,id'],
            'assignments.*.co_manager_id' => ['nullable', 'exists:users,id'],
            'assignments.*.scheduled_for' => ['nullable', 'date'],
        ]);

        $currentUser = $request->user();
        $created = 0;
        $skipped = [];

        foreach ($data['assignments'] as $row) {
            $employee = User::find($row['employee_id']);
            if (! $employee) { continue; }

            // Détermine le manager de l'entretien :
            //  - admin : peut choisir librement (par défaut : lui-même)
            //  - manager non-admin : c'est forcément lui, sur son subordonné
            if ($currentUser->isAdmin()) {
                $managerId = $row['manager_id'] ?? $currentUser->id;
                $manager = User::find($managerId);
                if (! $manager || ! in_array($manager->role, [User::ROLE_MANAGER, User::ROLE_ADMIN], true)) {
                    $skipped[] = "{$employee->name} : manager invalide";
                    continue;
                }
                if ($manager->id === $employee->id) {
                    $skipped[] = "{$employee->name} : le salarié ne peut pas être son propre manager";
                    continue;
                }
            } else {
                if ($employee->manager_id !== $currentUser->id) {
                    $skipped[] = "{$employee->name} : vous n'êtes pas son manager";
                    continue;
                }
                $managerId = $currentUser->id;
            }

            // Unicité (employee_id + year) — on ignore silencieusement les doublons
            $exists = AnnualReview::where('employee_id', $employee->id)
                ->where('year', $data['year'])
                ->exists();
            if ($exists) {
                $skipped[] = "{$employee->name} : déjà un entretien pour {$data['year']}";
                continue;
            }

            // Co-évaluateur (optionnel, admin uniquement) — informatif + visibilité.
            $coManagerId = null;
            if ($currentUser->isAdmin() && ! empty($row['co_manager_id'])) {
                $coManager = User::find($row['co_manager_id']);
                if ($coManager
                    && in_array($coManager->role, [User::ROLE_MANAGER, User::ROLE_ADMIN], true)
                    && $coManager->id !== $employee->id
                    && $coManager->id !== $managerId) {
                    $coManagerId = $coManager->id;
                }
            }

            AnnualReview::create([
                'employee_id' => $employee->id,
                'manager_id' => $managerId,
                'co_manager_id' => $coManagerId,
                'year' => $data['year'],
                'scheduled_for' => $row['scheduled_for'] ?? null,
                'status' => AnnualReview::STATUS_SCHEDULED,
                'template_key' => ReviewTemplate::keyForPosition($employee->position),
            ]);
            $created++;
        }

        $msg = $created . ' entretien' . ($created > 1 ? 's' : '') . ' planifié' . ($created > 1 ? 's' : '');
        if (! empty($skipped)) {
            $msg .= ' — ignorés : ' . implode(' ; ', $skipped);
        }
        return redirect()->route('reviews.index')->with($created > 0 ? 'success' : 'error', $msg);
    }

    public function show(AnnualReview $review): Response
    {
        Gate::authorize('view', $review);

        $review->load([
            'employee:id,name,email,position,department,hired_on',
            'manager:id,name,email',
            'coManager:id,name,email',
        ]);

        return Inertia::render('Reviews/Show', [
            'review' => $this->serialize($review),
            'template' => $review->template(),
        ]);
    }

    /**
     * Vue HTML imprimable — le navigateur gère le "Enregistrer au format PDF".
     * (Pas d'Inertia : on renvoie un document Blade autonome.)
     */
    public function printable(AnnualReview $review)
    {
        Gate::authorize('view', $review);

        $review->load([
            'employee:id,name,email,position,department,hired_on',
            'manager:id,name,email',
            'coManager:id,name,email',
        ]);

        return response()->view('reviews.print', [
            'review' => $review,
            'template' => $review->template(),
        ]);
    }

    /**
     * Téléchargement direct en PDF via dompdf — le PDF est généré
     * côté serveur, sans passer par la boîte d'impression du navigateur.
     */
    public function downloadPdf(AnnualReview $review)
    {
        Gate::authorize('view', $review);

        $review->load([
            'employee:id,name,email,position,department,hired_on',
            'manager:id,name,email',
            'coManager:id,name,email',
        ]);

        $filename = sprintf(
            '%d_%s_Entretien.pdf',
            $review->year,
            \Illuminate\Support\Str::upper(\Illuminate\Support\Str::slug($review->employee->name, '_'))
        );

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reviews.pdf', [
            'review' => $review,
            'template' => $review->template(),
        ])->setPaper('a4')->setOption('defaultFont', 'DejaVu Sans');

        return $pdf->download($filename);
    }

    /** Le salarié enregistre / envoie sa partie. */
    public function employeeUpdate(Request $request, AnnualReview $review): RedirectResponse
    {
        Gate::authorize('employeeEdit', $review);

        $data = $request->validate([
            'header' => ['nullable', 'array'],
            'answers' => ['nullable', 'array'],
            'submit' => ['nullable', 'boolean'],
        ]);

        $template = $review->template();

        // Filtre les clés header autorisées pour l'employé
        $employeeHeaderKeys = collect($template['header'] ?? [])
            ->where('owner', 'employee')
            ->pluck('key')
            ->all();

        $existingHeader = $review->header ?? [];
        foreach (($data['header'] ?? []) as $k => $v) {
            if (in_array($k, $employeeHeaderKeys, true)) {
                $existingHeader[$k] = $v;
            }
        }
        $review->header = $existingHeader;

        // Les réponses salarié sont stockées telles quelles (filtrage par confiance :
        // le formulaire n'expose que les champs owner=employee, et la vue manager
        // n'affichera que les clés attendues de toute façon)
        $review->employee_answers = $data['answers'] ?? [];

        $submit = (bool) ($data['submit'] ?? false);
        if ($submit) {
            $review->status = AnnualReview::STATUS_READY_FOR_MANAGER;
        } elseif ($review->status === AnnualReview::STATUS_SCHEDULED) {
            $review->status = AnnualReview::STATUS_EMPLOYEE_DRAFT;
        }
        $review->save();

        return back()->with('success', $submit
            ? 'Auto-évaluation envoyée au manager'
            : 'Brouillon enregistré');
    }

    /** Le manager enregistre / finalise sa partie. */
    public function managerUpdate(Request $request, AnnualReview $review): RedirectResponse
    {
        Gate::authorize('managerEdit', $review);

        $data = $request->validate([
            'header' => ['nullable', 'array'],
            'answers' => ['nullable', 'array'],
            'finalize' => ['nullable', 'boolean'],
        ]);

        $template = $review->template();

        $managerHeaderKeys = collect($template['header'] ?? [])
            ->where('owner', 'manager')
            ->pluck('key')
            ->all();

        $existingHeader = $review->header ?? [];
        foreach (($data['header'] ?? []) as $k => $v) {
            if (in_array($k, $managerHeaderKeys, true)) {
                $existingHeader[$k] = $v;
            }
        }
        $review->header = $existingHeader;

        $review->manager_answers = $data['answers'] ?? [];

        $finalize = (bool) ($data['finalize'] ?? false);
        if ($finalize) {
            $review->status = AnnualReview::STATUS_COMPLETED;
        } elseif ($review->status === AnnualReview::STATUS_READY_FOR_MANAGER) {
            $review->status = AnnualReview::STATUS_MANAGER_DRAFT;
        }
        $review->save();

        return back()->with('success', $finalize
            ? 'Entretien prêt à être signé'
            : 'Brouillon manager enregistré');
    }

    public function sign(Request $request, AnnualReview $review): RedirectResponse
    {
        Gate::authorize('view', $review);

        $user = $request->user();
        $now = Carbon::now();
        $changed = false;

        if ($review->status === AnnualReview::STATUS_COMPLETED
            || $review->status === AnnualReview::STATUS_SIGNED) {
            if ($review->employee_id === $user->id && ! $review->employee_signed_at) {
                $review->employee_signed_at = $now;
                $changed = true;
            }
            if (($review->manager_id === $user->id || $user->isAdmin())
                && ! $review->manager_signed_at) {
                $review->manager_signed_at = $now;
                $changed = true;
            }
        } else {
            return back()->with('error', "L'entretien doit être finalisé par le manager avant signature.");
        }

        if ($changed && $review->employee_signed_at && $review->manager_signed_at) {
            $review->status = AnnualReview::STATUS_SIGNED;
        }

        $review->save();

        return back()->with('success', 'Signature enregistrée');
    }

    /** Modifie la date d'un entretien planifié (admin uniquement). */
    public function reschedule(Request $request, AnnualReview $review): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $data = $request->validate([
            'scheduled_for' => ['nullable', 'date'],
        ]);

        $review->update(['scheduled_for' => $data['scheduled_for'] ?? null]);

        return back()->with('success', 'Date de l\'entretien mise à jour');
    }

    public function destroy(AnnualReview $review): RedirectResponse
    {
        Gate::authorize('delete', $review);
        $review->delete();
        return redirect()->route('reviews.index')->with('success', 'Entretien supprimé');
    }

    private function serialize(AnnualReview $review): array
    {
        return [
            'id' => $review->id,
            'year' => $review->year,
            'scheduled_for' => optional($review->scheduled_for)->toDateString(),
            'status' => $review->status,
            'status_label' => $review->statusLabel(),
            'template_key' => $review->template_key,
            'header' => $review->header ?? (object) [],
            'employee_answers' => $review->employee_answers ?? (object) [],
            'manager_answers' => $review->manager_answers ?? (object) [],
            'employee_signed_at' => optional($review->employee_signed_at)->toIso8601String(),
            'manager_signed_at' => optional($review->manager_signed_at)->toIso8601String(),
            'employee' => $review->employee ? [
                'id' => $review->employee->id,
                'name' => $review->employee->name,
                'email' => $review->employee->email,
                'position' => $review->employee->position,
                'department' => $review->employee->department,
                'hired_on' => optional($review->employee->hired_on)->toDateString(),
            ] : null,
            'manager' => $review->manager ? [
                'id' => $review->manager->id,
                'name' => $review->manager->name,
                'email' => $review->manager->email,
            ] : null,
            'co_manager' => $review->coManager ? [
                'id' => $review->coManager->id,
                'name' => $review->coManager->name,
                'email' => $review->coManager->email,
            ] : null,
        ];
    }
}

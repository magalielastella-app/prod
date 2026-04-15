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

        $query = AnnualReview::with(['employee:id,name,email,position,department', 'manager:id,name'])
            ->orderByDesc('year')
            ->orderBy('scheduled_for');

        if ($user->isAdmin()) {
            // tout
        } elseif ($user->isManager()) {
            $query->where(function ($q) use ($user) {
                $q->where('manager_id', $user->id)
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
            'signed' => $r->isSigned(),
            'is_mine' => $r->employee_id === $user->id,
        ]);

        $employees = [];
        if ($user->isManager()) {
            $employees = User::query()
                ->where('role', User::ROLE_EMPLOYEE)
                ->when(! $user->isAdmin(), fn ($q) => $q->where('manager_id', $user->id))
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'position', 'department']);
        }

        return Inertia::render('Reviews/Index', [
            'reviews' => $reviews,
            'employees' => $employees,
            'defaultYear' => (int) Carbon::now()->year,
            'can' => [
                'create' => $user->isManager(),
            ],
        ]);
    }

    /** Le manager planifie un nouvel entretien. */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', AnnualReview::class);

        $data = $request->validate([
            'employee_id' => ['required', 'exists:users,id'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'scheduled_for' => ['nullable', 'date'],
        ]);

        $employee = User::findOrFail($data['employee_id']);

        if (! $request->user()->isAdmin()
            && $employee->manager_id !== $request->user()->id) {
            abort(403, "Vous n'êtes pas le manager de ce salarié.");
        }

        AnnualReview::create([
            'employee_id' => $employee->id,
            'manager_id' => $request->user()->id,
            'year' => $data['year'],
            'scheduled_for' => $data['scheduled_for'] ?? null,
            'status' => AnnualReview::STATUS_SCHEDULED,
            'template_key' => ReviewTemplate::keyForPosition($employee->position),
        ]);

        return redirect()->route('reviews.index')->with('success', 'Entretien planifié');
    }

    public function show(AnnualReview $review): Response
    {
        Gate::authorize('view', $review);

        $review->load([
            'employee:id,name,email,position,department,hired_on',
            'manager:id,name,email',
        ]);

        return Inertia::render('Reviews/Show', [
            'review' => $this->serialize($review),
            'template' => $review->template(),
        ]);
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
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AnnualReview;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class AnnualReviewController extends Controller
{
    /** List reviews the current user can see. */
    public function index(Request $request): Response
    {
        $user = $request->user();

        $query = AnnualReview::with(['employee:id,name,email,position,department', 'manager:id,name'])
            ->orderByDesc('year')
            ->orderBy('scheduled_for');

        if ($user->isAdmin()) {
            // All reviews
        } elseif ($user->isManager()) {
            // Reviews the manager owns OR their own review
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

        // For managers: list of employees to propose a new review for
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

    /** Manager schedules a new review for an employee. */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', AnnualReview::class);

        $data = $request->validate([
            'employee_id' => ['required', 'exists:users,id'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'scheduled_for' => ['nullable', 'date'],
        ]);

        $employee = User::findOrFail($data['employee_id']);

        // Safety: non-admin managers can only plan a review for their own subordinates
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
        ]);
    }

    /** Employee saves their self-assessment (draft or submit). */
    public function employeeUpdate(Request $request, AnnualReview $review): RedirectResponse
    {
        Gate::authorize('employeeEdit', $review);

        $data = $request->validate([
            'self_achievements' => ['nullable', 'string'],
            'self_difficulties' => ['nullable', 'string'],
            'self_skills_developed' => ['nullable', 'string'],
            'self_motivation' => ['nullable', 'string'],
            'previous_objectives' => ['nullable', 'array'],
            'previous_objectives.*.title' => ['nullable', 'string', 'max:255'],
            'previous_objectives.*.result' => ['nullable', 'string'],
            'previous_objectives.*.achievement' => ['nullable', 'string', 'max:50'],
            'employee_comments' => ['nullable', 'string'],
            'submit' => ['nullable', 'boolean'],
        ]);

        $submit = (bool) ($data['submit'] ?? false);
        unset($data['submit']);

        $review->fill($data);
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

    /** Manager saves their assessment (draft or finalize). */
    public function managerUpdate(Request $request, AnnualReview $review): RedirectResponse
    {
        Gate::authorize('managerEdit', $review);

        $data = $request->validate([
            'new_objectives' => ['nullable', 'array'],
            'new_objectives.*.title' => ['nullable', 'string', 'max:255'],
            'new_objectives.*.description' => ['nullable', 'string'],
            'new_objectives.*.deadline' => ['nullable', 'string', 'max:50'],
            'training_needs' => ['nullable', 'string'],
            'career_development' => ['nullable', 'string'],
            'manager_appreciation' => ['nullable', 'string'],
            'manager_areas_for_improvement' => ['nullable', 'string'],
            'overall_rating' => ['nullable', 'integer', 'between:1,5'],
            'manager_comments' => ['nullable', 'string'],
            'finalize' => ['nullable', 'boolean'],
        ]);

        $finalize = (bool) ($data['finalize'] ?? false);
        unset($data['finalize']);

        $review->fill($data);
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

    /** Employee or manager signs the review. */
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
            'self_achievements' => $review->self_achievements,
            'self_difficulties' => $review->self_difficulties,
            'self_skills_developed' => $review->self_skills_developed,
            'self_motivation' => $review->self_motivation,
            'previous_objectives' => $review->previous_objectives ?? [],
            'new_objectives' => $review->new_objectives ?? [],
            'training_needs' => $review->training_needs,
            'career_development' => $review->career_development,
            'manager_appreciation' => $review->manager_appreciation,
            'manager_areas_for_improvement' => $review->manager_areas_for_improvement,
            'overall_rating' => $review->overall_rating,
            'employee_comments' => $review->employee_comments,
            'manager_comments' => $review->manager_comments,
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

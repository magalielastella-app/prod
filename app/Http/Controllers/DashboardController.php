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
            'team' => $user->isAdmin()
                ? User::count()
                : ($user->isManager() ? User::where('manager_id', $user->id)->count() : null),
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

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'upcoming' => $upcoming,
            'currentYear' => $currentYear,
        ]);
    }
}

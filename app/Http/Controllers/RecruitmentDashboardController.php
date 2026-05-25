<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\CvAnalysis;
use App\Models\InterviewEvent;
use App\Models\JobOffer;
use Carbon\Carbon;
use Inertia\Inertia;

class RecruitmentDashboardController extends Controller
{
    public function __invoke()
    {
        $now = Carbon::now();
        $weekStart = $now->copy()->startOfWeek();
        $weekEnd = $now->copy()->endOfWeek();

        $stats = [
            'total_candidates' => Candidate::count(),
            'active_offers' => JobOffer::where('status', 'active')->count(),
            'interviews_this_week' => InterviewEvent::whereBetween('scheduled_at', [$weekStart, $weekEnd])->count(),
            'analyses_saved' => CvAnalysis::count(),
        ];

        $recentCandidates = Candidate::latest()
            ->take(5)
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'full_name' => $c->full_name,
                'email' => $c->email,
                'status' => $c->status,
                'created_at' => $c->created_at->format('d/m/Y'),
            ]);

        $upcomingInterviews = InterviewEvent::with(['candidate', 'jobOffer'])
            ->where('scheduled_at', '>=', $now)
            ->where('status', 'scheduled')
            ->orderBy('scheduled_at')
            ->take(5)
            ->get()
            ->map(fn ($e) => [
                'id' => $e->id,
                'title' => $e->title,
                'candidate_name' => $e->candidate->full_name,
                'job_offer_title' => $e->jobOffer?->title,
                'scheduled_at' => $e->scheduled_at->format('d/m/Y H:i'),
                'location' => $e->location,
            ]);

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentCandidates' => $recentCandidates,
            'upcomingInterviews' => $upcomingInterviews,
        ]);
    }
}

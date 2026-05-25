<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\InterviewReport;
use App\Models\JobOffer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InterviewReportController extends Controller
{
    public function index(Request $request)
    {
        $query = InterviewReport::with(['candidate', 'jobOffer', 'interviewer']);

        if ($candidateId = $request->get('candidate_id')) {
            $query->where('candidate_id', $candidateId);
        }

        $reports = $query->latest('interview_date')->paginate(20)->withQueryString();

        $candidates = Candidate::orderBy('last_name')->get(['id', 'first_name', 'last_name']);
        $offers = JobOffer::orderBy('title')->get(['id', 'title']);

        return Inertia::render('Reports/Index', [
            'reports' => $reports,
            'candidates' => $candidates,
            'offers' => $offers,
            'filters' => [
                'candidate_id' => $request->get('candidate_id', ''),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'job_offer_id' => 'nullable|exists:job_offers,id',
            'interview_date' => 'required|date',
            'rating' => 'nullable|integer|min:1|max:5',
            'strengths' => 'nullable|string',
            'weaknesses' => 'nullable|string',
            'notes' => 'nullable|string',
            'recommendation' => 'nullable|in:hire,maybe,reject',
        ]);

        $validated['interviewer_id'] = $request->user()->id;

        InterviewReport::create($validated);

        return redirect()->route('reports.index')->with('success', 'Compte-rendu créé.');
    }

    public function update(Request $request, InterviewReport $report)
    {
        $validated = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'job_offer_id' => 'nullable|exists:job_offers,id',
            'interview_date' => 'required|date',
            'rating' => 'nullable|integer|min:1|max:5',
            'strengths' => 'nullable|string',
            'weaknesses' => 'nullable|string',
            'notes' => 'nullable|string',
            'recommendation' => 'nullable|in:hire,maybe,reject',
        ]);

        $report->update($validated);

        return redirect()->back()->with('success', 'Compte-rendu mis à jour.');
    }

    public function destroy(InterviewReport $report)
    {
        $report->delete();

        return redirect()->route('reports.index')->with('success', 'Compte-rendu supprimé.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\JobPosition;
use App\Models\RecruitmentCampaign;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CandidateController extends Controller
{
    public function index(Request $request)
    {
        $query = Candidate::with(['campaign:id,title', 'jobPosition:id,name']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($campaignId = $request->get('campaign')) {
            $query->where('campaign_id', $campaignId);
        }

        $candidates = $query->latest()->paginate(20)->withQueryString();

        $campaigns = RecruitmentCampaign::orderBy('title')->get(['id', 'title', 'status']);
        $jobPositions = JobPosition::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Candidates/Index', [
            'candidates' => $candidates,
            'campaigns' => $campaigns,
            'jobPositions' => $jobPositions,
            'statuses' => Candidate::STATUSES,
            'filters' => [
                'search' => $request->get('search', ''),
                'status' => $request->get('status', ''),
                'campaign' => $request->get('campaign', ''),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'campaign_id' => 'nullable|exists:recruitment_campaigns,id',
            'job_position_id' => 'nullable|exists:job_positions,id',
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $candidate = Candidate::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'city' => $validated['city'] ?? null,
            'source' => $validated['source'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'campaign_id' => $validated['campaign_id'] ?? null,
            'job_position_id' => $validated['job_position_id'] ?? null,
            'status' => Candidate::STATUS_A_ANALYSER,
        ]);

        // Si un CV a été uploadé en même temps que la création
        if ($request->hasFile('cv')) {
            $file = $request->file('cv');
            $path = $file->store('cvs', 'local');

            $candidate->cvDocuments()->create([
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);
        }

        return redirect()->route('candidates.index')->with('success', 'Candidat ajouté avec succès.');
    }

    public function show(Candidate $candidate)
    {
        $candidate->load([
            'campaign:id,title',
            'jobPosition:id,name',
            'cvDocuments',
            'analyses.cvDocument',
            'interviewReports.jobOffer',
            'interviewReports.interviewer',
            'interviewEvents.jobOffer',
        ]);

        $campaigns = RecruitmentCampaign::where('status', 'active')
            ->orderBy('title')
            ->get(['id', 'title']);

        $jobPositions = JobPosition::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Candidates/Show', [
            'candidate' => $candidate,
            'campaigns' => $campaigns,
            'jobPositions' => $jobPositions,
            'statuses' => Candidate::STATUSES,
        ]);
    }

    public function update(Request $request, Candidate $candidate)
    {
        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:a_analyser,selectionne,rejete',
            'campaign_id' => 'nullable|exists:recruitment_campaigns,id',
            'job_position_id' => 'nullable|exists:job_positions,id',
        ]);

        $candidate->update($validated);

        return redirect()->back()->with('success', 'Candidat mis à jour.');
    }

    public function destroy(Candidate $candidate)
    {
        $candidate->delete();
        return redirect()->route('candidates.index')->with('success', 'Candidat supprimé.');
    }
}

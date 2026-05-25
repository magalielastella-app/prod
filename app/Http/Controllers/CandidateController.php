<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CandidateController extends Controller
{
    public function index(Request $request)
    {
        $query = Candidate::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $candidates = $query->latest()->paginate(20)->withQueryString();

        return Inertia::render('Candidates/Index', [
            'candidates' => $candidates,
            'filters' => [
                'search' => $request->get('search', ''),
                'status' => $request->get('status', ''),
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
        ]);

        Candidate::create($validated);

        return redirect()->route('candidates.index')->with('success', 'Candidat ajouté avec succès.');
    }

    public function show(Candidate $candidate)
    {
        $candidate->load([
            'cvDocuments',
            'analyses.cvDocument',
            'interviewReports.jobOffer',
            'interviewReports.interviewer',
            'interviewEvents.jobOffer',
        ]);

        return Inertia::render('Candidates/Show', [
            'candidate' => $candidate,
        ]);
    }

    public function update(Request $request, Candidate $candidate)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:255',
            'status' => 'nullable|in:new,screening,interview,offer,hired,rejected',
            'source' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
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

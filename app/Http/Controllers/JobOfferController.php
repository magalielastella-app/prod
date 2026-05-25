<?php

namespace App\Http\Controllers;

use App\Models\JobOffer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JobOfferController extends Controller
{
    public function index(Request $request)
    {
        $query = JobOffer::query();

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $offers = $query->latest()->paginate(20)->withQueryString();

        return Inertia::render('Offers/Index', [
            'offers' => $offers,
            'filters' => [
                'status' => $request->get('status', ''),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'contract_type' => 'nullable|in:CDI,CDD,Stage,Alternance,Interim',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'salary_range' => 'nullable|string|max:255',
            'status' => 'nullable|in:draft,active,archived',
        ]);

        $validated['created_by'] = $request->user()->id;

        if (($validated['status'] ?? 'draft') === 'active') {
            $validated['published_at'] = Carbon::now();
        }

        JobOffer::create($validated);

        return redirect()->route('offers.index')->with('success', 'Offre créée avec succès.');
    }

    public function show(JobOffer $jobOffer)
    {
        $jobOffer->load([
            'interviewReports.candidate',
            'interviewEvents.candidate',
            'createdBy',
        ]);

        return Inertia::render('Offers/Show', [
            'offer' => $jobOffer,
        ]);
    }

    public function update(Request $request, JobOffer $jobOffer)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'contract_type' => 'nullable|in:CDI,CDD,Stage,Alternance,Interim',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'salary_range' => 'nullable|string|max:255',
            'status' => 'nullable|in:draft,active,archived',
        ]);

        if (($validated['status'] ?? $jobOffer->status) === 'active' && !$jobOffer->published_at) {
            $validated['published_at'] = Carbon::now();
        }

        $jobOffer->update($validated);

        return redirect()->back()->with('success', 'Offre mise à jour.');
    }

    public function archive(JobOffer $jobOffer)
    {
        $jobOffer->update([
            'status' => 'archived',
            'archived_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', 'Offre archivée.');
    }

    public function destroy(JobOffer $jobOffer)
    {
        $jobOffer->delete();

        return redirect()->route('offers.index')->with('success', 'Offre supprimée.');
    }
}

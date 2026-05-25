<?php

namespace App\Http\Controllers;

use App\Models\JobOffer;
use App\Models\RecruitmentCampaign;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RecruitmentCampaignController extends Controller
{
    public function index(): Response
    {
        $campaigns = RecruitmentCampaign::withCount('candidates')
            ->with('jobOffer:id,title')
            ->orderByDesc('created_at')
            ->get();

        $offers = JobOffer::where('status', 'active')->orderBy('title')->get(['id', 'title']);

        return Inertia::render('Campaigns/Index', [
            'campaigns' => $campaigns,
            'offers' => $offers,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'job_offer_id' => ['nullable', 'exists:job_offers,id'],
        ]);

        RecruitmentCampaign::create($data);

        return back()->with('success', 'Campagne créée');
    }

    public function update(Request $request, RecruitmentCampaign $campaign): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'job_offer_id' => ['nullable', 'exists:job_offers,id'],
            'status' => ['nullable', 'in:active,closed'],
        ]);

        $campaign->update($data);

        return back()->with('success', 'Campagne mise à jour');
    }

    public function destroy(RecruitmentCampaign $campaign): RedirectResponse
    {
        $campaign->delete();
        return back()->with('success', 'Campagne supprimée');
    }
}

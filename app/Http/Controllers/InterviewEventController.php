<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\InterviewEvent;
use App\Models\JobOffer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InterviewEventController extends Controller
{
    public function index(Request $request)
    {
        $query = InterviewEvent::with(['candidate', 'jobOffer', 'interviewer']);

        if ($from = $request->get('from')) {
            $query->where('scheduled_at', '>=', Carbon::parse($from)->startOfDay());
        }

        if ($to = $request->get('to')) {
            $query->where('scheduled_at', '<=', Carbon::parse($to)->endOfDay());
        }

        $events = $query->orderBy('scheduled_at')->paginate(30)->withQueryString();

        $candidates = Candidate::orderBy('last_name')->get(['id', 'first_name', 'last_name']);
        $offers = JobOffer::where('status', 'active')->orderBy('title')->get(['id', 'title']);
        $interviewers = User::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Agenda/Index', [
            'events' => $events,
            'candidates' => $candidates,
            'offers' => $offers,
            'interviewers' => $interviewers,
            'filters' => [
                'from' => $request->get('from', ''),
                'to' => $request->get('to', ''),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'job_offer_id' => 'nullable|exists:job_offers,id',
            'interviewer_id' => 'nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'nullable|integer|min:15|max:480',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        InterviewEvent::create($validated);

        return redirect()->route('agenda.index')->with('success', 'Entretien planifié.');
    }

    public function update(Request $request, InterviewEvent $event)
    {
        $validated = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'job_offer_id' => 'nullable|exists:job_offers,id',
            'interviewer_id' => 'nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'nullable|integer|min:15|max:480',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:scheduled,completed,cancelled',
        ]);

        $event->update($validated);

        return redirect()->back()->with('success', 'Événement mis à jour.');
    }

    public function destroy(InterviewEvent $event)
    {
        $event->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'Événement annulé.');
    }
}

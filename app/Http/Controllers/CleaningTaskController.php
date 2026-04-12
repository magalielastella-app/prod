<?php

namespace App\Http\Controllers;

use App\Models\CleaningTask;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CleaningTaskController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        CleaningTask::create($request->validate([
            'zone' => ['required', 'string', 'max:255'],
            'frequency' => ['required', 'string', 'in:' . implode(',', CleaningTask::FREQUENCIES)],
            'agent' => ['nullable', 'string', 'max:255'],
        ]));
        return back()->with('success', 'Tâche ajoutée');
    }

    /** Marque la tâche comme effectuée aujourd'hui par l'agent fourni. */
    public function markDone(Request $request, CleaningTask $cleaningTask): RedirectResponse
    {
        $data = $request->validate([
            'agent' => ['required', 'string', 'max:255'],
        ]);
        $cleaningTask->update([
            'last_done' => Carbon::today(),
            'agent' => $data['agent'],
        ]);
        return back()->with('success', 'Nettoyage enregistré');
    }

    public function destroy(CleaningTask $cleaningTask): RedirectResponse
    {
        $cleaningTask->delete();
        return back()->with('success', 'Tâche supprimée');
    }
}

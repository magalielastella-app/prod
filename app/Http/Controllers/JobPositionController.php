<?php

namespace App\Http\Controllers;

use App\Models\JobPosition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JobPositionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:job_positions,name'],
        ]);

        JobPosition::create(['name' => $data['name'], 'is_default' => false]);

        return back()->with('success', 'Métier ajouté');
    }

    public function destroy(JobPosition $jobPosition): RedirectResponse
    {
        if ($jobPosition->is_default) {
            return back()->with('error', 'Impossible de supprimer un métier par défaut.');
        }
        $jobPosition->delete();
        return back()->with('success', 'Métier supprimé');
    }
}

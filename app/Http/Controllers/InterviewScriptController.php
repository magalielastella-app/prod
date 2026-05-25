<?php

namespace App\Http\Controllers;

use App\Models\InterviewScript;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InterviewScriptController extends Controller
{
    public function index()
    {
        $scripts = InterviewScript::with('createdBy')
            ->latest()
            ->paginate(20);

        return Inertia::render('Scripts/Index', [
            'scripts' => $scripts,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sections' => 'required|array|min:1',
            'sections.*.title' => 'required|string|max:255',
            'sections.*.content' => 'nullable|string',
        ]);

        $validated['created_by'] = $request->user()->id;

        InterviewScript::create($validated);

        return redirect()->route('scripts.index')->with('success', 'Script créé avec succès.');
    }

    public function show(InterviewScript $script)
    {
        $script->load('createdBy');

        return Inertia::render('Scripts/Show', [
            'script' => $script,
        ]);
    }

    public function update(Request $request, InterviewScript $script)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sections' => 'required|array|min:1',
            'sections.*.title' => 'required|string|max:255',
            'sections.*.content' => 'nullable|string',
        ]);

        $script->update($validated);

        return redirect()->back()->with('success', 'Script mis à jour.');
    }

    public function destroy(InterviewScript $script)
    {
        $script->delete();

        return redirect()->route('scripts.index')->with('success', 'Script supprimé.');
    }
}

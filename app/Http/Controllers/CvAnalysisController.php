<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\CvAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class CvAnalysisController extends Controller
{
    public function index(Request $request)
    {
        $analyses = CvAnalysis::with(['candidate', 'cvDocument'])
            ->latest()
            ->paginate(20);

        $candidates = Candidate::orderBy('last_name')->get(['id', 'first_name', 'last_name']);

        return Inertia::render('Analyses/Index', [
            'analyses' => $analyses,
            'candidates' => $candidates,
            'apiKeyConfigured' => !empty(config('services.anthropic.api_key')),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'cv_document_id' => 'nullable|exists:cv_documents,id',
            'prompt' => 'nullable|string',
        ]);

        $apiKey = config('services.anthropic.api_key');

        if (empty($apiKey)) {
            return redirect()->back()->withErrors([
                'api' => 'Configurez ANTHROPIC_API_KEY pour activer l\'analyse IA.',
            ]);
        }

        $candidate = Candidate::with('cvDocuments')->findOrFail($validated['candidate_id']);

        $prompt = $validated['prompt'] ?? "Analyse le profil de ce candidat et fournis un résumé de ses compétences, expériences et points forts. Candidat : {$candidate->full_name}";

        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-sonnet-4-20250514',
                'max_tokens' => 2048,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            $result = $response->json();
            $analysisText = $result['content'][0]['text'] ?? 'Aucune analyse disponible.';
            $modelUsed = $result['model'] ?? 'claude-sonnet-4-20250514';
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'api' => 'Erreur lors de l\'appel à l\'API Claude : ' . $e->getMessage(),
            ]);
        }

        CvAnalysis::create([
            'candidate_id' => $validated['candidate_id'],
            'cv_document_id' => $validated['cv_document_id'] ?? null,
            'analysis' => $analysisText,
            'prompt_used' => $prompt,
            'model_used' => $modelUsed,
        ]);

        return redirect()->route('analyses.index')->with('success', 'Analyse sauvegardée.');
    }

    /** Comparer plusieurs CV entre eux via Claude. */
    public function compare(Request $request)
    {
        $validated = $request->validate([
            'candidate_ids' => 'required|array|min:2|max:10',
            'candidate_ids.*' => 'exists:candidates,id',
            'job_context' => 'nullable|string',
        ]);

        $apiKey = config('services.anthropic.api_key');
        if (empty($apiKey)) {
            return redirect()->back()->withErrors([
                'api' => 'Configurez ANTHROPIC_API_KEY pour activer l\'analyse IA.',
            ]);
        }

        $candidates = Candidate::with('cvDocuments')
            ->whereIn('id', $validated['candidate_ids'])
            ->get();

        $candidateDescriptions = $candidates->map(function ($c) {
            $cvCount = $c->cvDocuments->count();
            return "- {$c->full_name}" . ($c->email ? " ({$c->email})" : '') . " — {$cvCount} CV enregistré(s)";
        })->implode("\n");

        $jobContext = $validated['job_context'] ?? '';
        $contextLine = $jobContext ? "\n\nContexte du poste : {$jobContext}" : '';

        $prompt = "Compare les candidats suivants pour un recrutement. Pour chacun, identifie les points forts et points faibles. Termine par une recommandation de classement.{$contextLine}\n\nCandidats :\n{$candidateDescriptions}";

        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-sonnet-4-20250514',
                'max_tokens' => 4096,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            $result = $response->json();
            $analysisText = $result['content'][0]['text'] ?? 'Aucune analyse disponible.';
            $modelUsed = $result['model'] ?? 'claude-sonnet-4-20250514';
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'api' => 'Erreur : ' . $e->getMessage(),
            ]);
        }

        // Sauvegarder une analyse pour le premier candidat de la liste (comme analyse comparative)
        CvAnalysis::create([
            'candidate_id' => $candidates->first()->id,
            'analysis' => "[COMPARAISON]\n\n" . $analysisText,
            'prompt_used' => $prompt,
            'model_used' => $modelUsed,
        ]);

        return redirect()->route('analyses.index')->with('success', 'Analyse comparative sauvegardée.');
    }

    public function destroy(CvAnalysis $analysis)
    {
        $analysis->delete();

        return redirect()->back()->with('success', 'Analyse supprimée.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CandidateAssistantController extends Controller
{
    /**
     * Assistant IA conversationnel : répond à des questions sur le pool
     * de candidats (ex : "qui habite le plus proche de La Tronche ?",
     * "combien de candidats sont sélectionnés ?", etc.).
     *
     * Les données candidats sont injectées comme contexte à Claude.
     */
    public function ask(Request $request): JsonResponse
    {
        $request->validate([
            'question' => ['required', 'string', 'max:2000'],
            'campaign_id' => ['nullable', 'exists:recruitment_campaigns,id'],
        ]);

        $apiKey = config('services.anthropic.api_key');
        if (empty($apiKey)) {
            return response()->json([
                'answer' => "L'assistant IA nécessite une clé API. Configurez ANTHROPIC_API_KEY dans les variables d'environnement.",
            ]);
        }

        // Récupère les candidats (filtrés par campagne si précisé)
        $query = Candidate::with('campaign:id,title');
        if ($request->campaign_id) {
            $query->where('campaign_id', $request->campaign_id);
        }
        $candidates = $query->orderBy('last_name')->get();

        // Formate les candidats comme contexte structuré
        $candidateContext = $candidates->map(function ($c) {
            return implode(' | ', array_filter([
                "Nom: {$c->full_name}",
                $c->email ? "Email: {$c->email}" : null,
                $c->phone ? "Tél: {$c->phone}" : null,
                $c->city ? "Ville: {$c->city}" : null,
                "Statut: " . (Candidate::STATUSES[$c->status] ?? $c->status),
                $c->source ? "Source: {$c->source}" : null,
                $c->campaign ? "Campagne: {$c->campaign->title}" : null,
                $c->notes ? "Notes: " . \Illuminate\Support\Str::limit($c->notes, 200) : null,
            ]));
        })->implode("\n");

        $systemPrompt = "Tu es un assistant RH pour une entreprise. Tu as accès à la base de candidats ci-dessous. "
            . "Réponds aux questions de l'utilisateur en te basant UNIQUEMENT sur ces données. "
            . "Si tu ne peux pas répondre avec certitude, dis-le. "
            . "Réponds en français, de manière concise et structurée.\n\n"
            . "CANDIDATS (" . $candidates->count() . " au total) :\n"
            . $candidateContext;

        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-sonnet-4-20250514',
                'max_tokens' => 1024,
                'system' => $systemPrompt,
                'messages' => [
                    ['role' => 'user', 'content' => $request->question],
                ],
            ]);

            $result = $response->json();
            $answer = $result['content'][0]['text'] ?? 'Pas de réponse.';
        } catch (\Exception $e) {
            $answer = 'Erreur de communication avec Claude : ' . $e->getMessage();
        }

        return response()->json(['answer' => $answer]);
    }
}

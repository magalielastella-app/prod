<?php

namespace App\Http\Controllers;

use App\Models\ReviewTemplateModel;
use App\Support\ReviewTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TemplateController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorizeAdmin($request);

        $templates = collect(ReviewTemplate::all())->map(fn (array $t) => [
            'key' => $t['key'],
            'label' => $t['label'],
            'section_count' => count($t['sections'] ?? []),
            'field_count' => collect($t['sections'] ?? [])
                ->flatMap(fn ($s) => $s['fields'] ?? [])
                ->count(),
        ]);

        return Inertia::render('Templates/Index', [
            'templates' => $templates,
        ]);
    }

    public function edit(Request $request, string $key): Response
    {
        $this->authorizeAdmin($request);

        $template = ReviewTemplate::get($key);

        return Inertia::render('Templates/Edit', [
            'template' => $template,
        ]);
    }

    public function update(Request $request, string $key): RedirectResponse
    {
        $this->authorizeAdmin($request);

        // Validation de la structure (les champs auxiliaires comme rows /
        // hint / evaluation_options / options ne sont pas listés mais ne
        // sont pas filtrés non plus : on stocke l'input brut juste après).
        $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'header' => ['nullable', 'array'],
            'header.*.key' => ['required', 'string'],
            'header.*.label' => ['required', 'string'],
            'header.*.owner' => ['required', 'string'],
            'sections' => ['required', 'array', 'min:1'],
            'sections.*.title' => ['required', 'string'],
            'sections.*.fields' => ['required', 'array'],
            'sections.*.fields.*.type' => ['required', 'string'],
            'sections.*.fields.*.key' => ['required', 'string'],
            'sections.*.fields.*.question' => ['nullable', 'string'],
            'sections.*.fields.*.owner' => ['nullable', 'string'],
        ]);

        $label = $request->input('label');
        $header = $request->input('header', []);
        $sections = $request->input('sections', []);

        ReviewTemplateModel::updateOrCreate(
            ['key' => $key],
            [
                'label' => $label,
                'header' => $header,
                'sections' => $sections,
            ]
        );

        return redirect()->route('templates.index')
            ->with('success', 'Trame « ' . $label . ' » mise à jour');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless(optional($request->user())->isAdmin(), 403);
    }
}

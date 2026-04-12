<?php

namespace App\Http\Controllers;

use App\Models\CompanyInfo;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    /** Vue Outils : société + bibliothèque de documents. */
    public function index(): Response
    {
        return Inertia::render('Tools/Index', [
            'company' => CompanyInfo::current(),
            'documents' => Document::orderByDesc('created_at')->get(),
            'categories' => Document::CATEGORIES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:' . implode(',', Document::CATEGORIES)],
            'description' => ['nullable', 'string'],
            'file' => ['required', 'file', 'max:20480'], // 20 Mo max
        ]);

        $file = $request->file('file');
        $path = $file->store('documents', 'public');

        Document::create([
            'title' => $data['title'],
            'category' => $data['category'],
            'description' => $data['description'] ?? null,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        return back()->with('success', 'Document ajouté');
    }

    public function update(Request $request, Document $document): RedirectResponse
    {
        $document->update($request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:' . implode(',', Document::CATEGORIES)],
            'description' => ['nullable', 'string'],
        ]));
        return back()->with('success', 'Document mis à jour');
    }

    public function download(Document $document): StreamedResponse
    {
        abort_unless($document->file_path && Storage::disk('public')->exists($document->file_path), 404);
        return Storage::disk('public')->download(
            $document->file_path,
            $document->original_name ?? $document->title
        );
    }

    public function destroy(Document $document): RedirectResponse
    {
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }
        $document->delete();
        return back()->with('success', 'Document supprimé');
    }
}

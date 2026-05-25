<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\CvDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CvDocumentController extends Controller
{
    public function store(Request $request, Candidate $candidate)
    {
        $request->validate([
            'cv_file' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $file = $request->file('cv_file');
        $path = $file->store('cvs', 'local');

        $candidate->cvDocuments()->create([
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);

        return redirect()->back()->with('success', 'CV uploadé avec succès.');
    }

    public function download(CvDocument $cvDocument)
    {
        $path = Storage::disk('local')->path($cvDocument->file_path);

        return response()->download($path, $cvDocument->original_name);
    }

    public function destroy(CvDocument $cvDocument)
    {
        Storage::disk('local')->delete($cvDocument->file_path);
        $cvDocument->delete();

        return redirect()->back()->with('success', 'CV supprimé.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\SentEmail;
use App\Models\Candidate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class EmailTemplateController extends Controller
{
    public function index(): Response
    {
        $templates = EmailTemplate::orderByDesc('updated_at')->get();
        $sentEmails = SentEmail::with(['candidate:id,first_name,last_name', 'template:id,name'])
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(fn (SentEmail $e) => [
                'id' => $e->id,
                'candidate_name' => $e->candidate ? $e->candidate->first_name . ' ' . $e->candidate->last_name : '—',
                'template_name' => $e->template?->name,
                'to_email' => $e->to_email,
                'subject' => $e->subject,
                'status' => $e->status,
                'sent_at' => $e->created_at->format('d/m/Y H:i'),
            ]);

        return Inertia::render('Emails/Index', [
            'templates' => $templates,
            'sentEmails' => $sentEmails,
        ]);
    }

    public function storeTemplate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        EmailTemplate::create($data);

        return back()->with('success', 'Modèle d\'email créé');
    }

    public function updateTemplate(Request $request, EmailTemplate $template): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $template->update($data);

        return back()->with('success', 'Modèle mis à jour');
    }

    public function destroyTemplate(EmailTemplate $template): RedirectResponse
    {
        $template->delete();
        return back()->with('success', 'Modèle supprimé');
    }

    /** Envoyer un email à un candidat. */
    public function send(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'candidate_id' => ['required', 'exists:candidates,id'],
            'email_template_id' => ['nullable', 'exists:email_templates,id'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        $candidate = Candidate::findOrFail($data['candidate_id']);

        if (! $candidate->email) {
            return back()->with('error', 'Ce candidat n\'a pas d\'adresse email.');
        }

        $status = 'sent';
        try {
            Mail::raw($data['body'], function ($message) use ($candidate, $data) {
                $message->to($candidate->email)
                    ->subject($data['subject']);
            });
        } catch (\Throwable $e) {
            $status = 'failed';
        }

        SentEmail::create([
            'candidate_id' => $candidate->id,
            'email_template_id' => $data['email_template_id'] ?? null,
            'to_email' => $candidate->email,
            'subject' => $data['subject'],
            'body' => $data['body'],
            'status' => $status,
        ]);

        return back()->with(
            $status === 'sent' ? 'success' : 'error',
            $status === 'sent' ? 'Email envoyé à ' . $candidate->email : 'Échec d\'envoi — vérifiez la configuration SMTP.'
        );
    }
}

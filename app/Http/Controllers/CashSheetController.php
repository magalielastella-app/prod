<?php

namespace App\Http\Controllers;

use App\Models\CashSheet;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CashSheetController extends Controller
{
    public function index(Request $request): Response
    {
        // Mois affiché (par défaut : mois courant)
        $month = $request->string('month')->value()
            ?: Carbon::now()->format('Y-m');
        $start = Carbon::parse($month . '-01')->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $sheets = CashSheet::whereBetween('date', [$start, $end])
            ->orderBy('date')->get();

        return Inertia::render('Cash/Index', [
            'month' => $month,
            'sheets' => $sheets,
            'paymentFields' => CashSheet::PAYMENT_FIELDS,
            'summary' => [
                'ca_total' => (float) $sheets->sum('ca'),
                'ca_plateforme' => (float) $sheets->sum('ca_plateforme'),
                'cb' => (float) $sheets->sum('cb'),
                'cb_sans_contact' => (float) $sheets->sum('cb_sans_contact'),
                'espece' => (float) $sheets->sum('espece'),
                'ticket_restaurant' => (float) $sheets->sum('ticket_restaurant'),
                'borne' => (float) $sheets->sum('borne'),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        // Upsert : une seule feuille par date
        CashSheet::updateOrCreate(['date' => $data['date']], $data);
        return back()->with('success', 'Feuille de caisse enregistrée');
    }

    public function update(Request $request, CashSheet $cashSheet): RedirectResponse
    {
        $cashSheet->update($this->validated($request));
        return back()->with('success', 'Feuille mise à jour');
    }

    public function destroy(CashSheet $cashSheet): RedirectResponse
    {
        $cashSheet->delete();
        return back()->with('success', 'Feuille supprimée');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'date' => ['required', 'date'],
            'ca' => ['required', 'numeric', 'min:0'],
            'ca_plateforme' => ['required', 'numeric', 'min:0'],
            'cb' => ['required', 'numeric', 'min:0'],
            'cb_sans_contact' => ['required', 'numeric', 'min:0'],
            'espece' => ['required', 'numeric', 'min:0'],
            'ticket_restaurant' => ['required', 'numeric', 'min:0'],
            'borne' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}

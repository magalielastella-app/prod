<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        Delivery::create($request->validate([
            'date' => ['required', 'date'],
            'supplier' => ['required', 'string', 'max:255'],
            'product' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'unit' => ['nullable', 'string', 'max:20'],
            'temp_delivery' => ['nullable', 'numeric'],
            'dlc' => ['nullable', 'date'],
            'compliant' => ['required', 'boolean'],
        ]));
        return back()->with('success', 'Réception enregistrée');
    }

    public function destroy(Delivery $delivery): RedirectResponse
    {
        $delivery->delete();
        return back()->with('success', 'Réception supprimée');
    }
}

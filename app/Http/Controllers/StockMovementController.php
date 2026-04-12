<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{
    /** Sortie manuelle de stock (consommation, perte, vol, casse...). */
    public function out(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        if ((float) $data['quantity'] > (float) $product->quantity) {
            return back()->withErrors([
                'quantity' => "Quantité supérieure au stock ({$product->quantity} {$product->unit})",
            ]);
        }

        DB::transaction(function () use ($product, $data) {
            $product->decrement('quantity', $data['quantity']);
            StockMovement::create([
                'product_id' => $product->id,
                'type' => StockMovement::TYPE_OUT,
                'quantity' => $data['quantity'],
                'reason' => $data['reason'],
                'source_type' => 'manual',
                'date' => Carbon::today(),
                'user' => auth()->user()?->name,
            ]);
        });

        return back()->with('success', 'Sortie de stock enregistrée');
    }

    /** Entrée manuelle (ajustement d'inventaire hors facture). */
    public function in(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($product, $data) {
            $product->increment('quantity', $data['quantity']);
            StockMovement::create([
                'product_id' => $product->id,
                'type' => StockMovement::TYPE_IN,
                'quantity' => $data['quantity'],
                'reason' => $data['reason'],
                'source_type' => 'manual',
                'date' => Carbon::today(),
                'user' => auth()->user()?->name,
            ]);
        });

        return back()->with('success', 'Entrée de stock enregistrée');
    }
}

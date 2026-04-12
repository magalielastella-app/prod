<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SupplierProductController extends Controller
{
    /** Ajoute une ligne au cadencier d'un fournisseur. */
    public function store(Request $request, Supplier $supplier): RedirectResponse
    {
        $data = $this->validated($request);
        $supplier->cadencier()->create($data);
        return back()->with('success', 'Ligne ajoutée au cadencier');
    }

    public function update(Request $request, SupplierProduct $cadencier): RedirectResponse
    {
        $cadencier->update($this->validated($request));
        return back()->with('success', 'Ligne mise à jour');
    }

    public function destroy(SupplierProduct $cadencier): RedirectResponse
    {
        $cadencier->delete();
        return back()->with('success', 'Ligne supprimée');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'product_id' => ['nullable', 'exists:products,id'],
            'reference' => ['nullable', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['nullable', 'string', 'max:20'],
            'pack_size' => ['nullable', 'numeric', 'min:0'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'usual_quantity' => ['nullable', 'numeric', 'min:0'],
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SupplierController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Purchasing/Index', [
            'suppliers' => Supplier::withCount(['cadencier', 'invoices'])
                ->orderBy('name')->get(),
            'recentInvoices' => PurchaseInvoice::with('supplier')
                ->orderByDesc('date')->take(10)->get(),
        ]);
    }

    public function show(Supplier $supplier): Response
    {
        $supplier->load(['cadencier.product', 'invoices' => fn ($q) => $q->orderByDesc('date')]);

        return Inertia::render('Purchasing/Supplier', [
            'supplier' => $supplier,
            'products' => Product::orderBy('name')->get(['id', 'name', 'unit']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Supplier::create($this->validated($request));
        return back()->with('success', 'Fournisseur créé');
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($this->validated($request));
        return back()->with('success', 'Fournisseur mis à jour');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Fournisseur supprimé');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'order_day' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}

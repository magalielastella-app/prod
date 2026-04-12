<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /** Catégories prédéfinies, utilisées dans le select du formulaire. */
    public const CATEGORIES = [
        'Viandes', 'Poissons', 'Légumes', 'Fruits',
        'Produits laitiers', 'Épicerie', 'Boissons', 'Surgelés',
    ];

    public function index(Request $request): Response
    {
        $query = Product::query();

        if ($search = $request->string('search')->trim()->value()) {
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('supplier', 'like', "%{$search}%"));
        }
        if ($cat = $request->string('category')->value()) {
            $query->where('category', $cat);
        }

        return Inertia::render('Inventory/Index', [
            'products' => $query->orderBy('name')->get(),
            'categories' => self::CATEGORIES,
            'filters' => $request->only(['search', 'category']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Product::create($this->validated($request));
        return redirect()->route('products.index')->with('success', 'Produit ajouté');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $product->update($this->validated($request));
        return redirect()->route('products.index')->with('success', 'Produit mis à jour');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produit supprimé');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'unit' => ['nullable', 'string', 'max:20'],
            'min_threshold' => ['nullable', 'numeric', 'min:0'],
            'expiration' => ['nullable', 'date'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'supplier' => ['nullable', 'string', 'max:255'],
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PurchaseInvoiceController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Purchasing/Invoices', [
            'invoices' => PurchaseInvoice::with(['supplier', 'items'])
                ->orderByDesc('date')->paginate(50),
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('Purchasing/InvoiceForm', [
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
            'products' => Product::orderBy('name')->get(['id', 'name', 'unit']),
            'preselectedSupplier' => $request->integer('supplier_id'),
            'cadencierBySupplier' => Supplier::with('cadencier')->get()->mapWithKeys(
                fn ($s) => [$s->id => $s->cadencier]
            ),
        ]);
    }

    public function show(PurchaseInvoice $purchaseInvoice): Response
    {
        $purchaseInvoice->load(['supplier', 'items.product']);
        return Inertia::render('Purchasing/InvoiceShow', [
            'invoice' => $purchaseInvoice,
        ]);
    }

    /**
     * Enregistre la facture et réalimente automatiquement les stocks des
     * produits concernés via des StockMovement de type "in".
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'invoice_number' => ['nullable', 'string', 'max:100'],
            'date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.label' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit' => ['nullable', 'string', 'max:20'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($data) {
            $invoice = PurchaseInvoice::create([
                'supplier_id' => $data['supplier_id'],
                'invoice_number' => $data['invoice_number'] ?? null,
                'date' => $data['date'],
                'notes' => $data['notes'] ?? null,
                'total' => 0,
            ]);

            $total = 0;
            foreach ($data['items'] as $line) {
                $qty = (float) $line['quantity'];
                $unitPrice = (float) ($line['unit_price'] ?? 0);
                $lineTotal = round($qty * $unitPrice, 2);
                $total += $lineTotal;

                $invoice->items()->create([
                    'product_id' => $line['product_id'] ?? null,
                    'label' => $line['label'],
                    'quantity' => $qty,
                    'unit' => $line['unit'] ?? null,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ]);

                // Entrée en stock automatique si la ligne est liée à un produit
                if (!empty($line['product_id'])) {
                    $product = Product::find($line['product_id']);
                    $product->increment('quantity', $qty);

                    StockMovement::create([
                        'product_id' => $product->id,
                        'type' => StockMovement::TYPE_IN,
                        'quantity' => $qty,
                        'reason' => 'Facture fournisseur',
                        'source_type' => 'purchase_invoice',
                        'source_id' => $invoice->id,
                        'date' => $invoice->date,
                        'user' => auth()->user()?->name,
                    ]);
                }
            }

            $invoice->update(['total' => $total]);
        });

        return redirect()->route('invoices.index')
            ->with('success', 'Facture enregistrée, stock mis à jour');
    }

    public function destroy(PurchaseInvoice $purchaseInvoice): RedirectResponse
    {
        DB::transaction(function () use ($purchaseInvoice) {
            // Revert stock entries liés à cette facture
            $movements = StockMovement::where('source_type', 'purchase_invoice')
                ->where('source_id', $purchaseInvoice->id)->get();
            foreach ($movements as $m) {
                if ($m->product) {
                    $m->product->decrement('quantity', $m->quantity);
                }
                $m->delete();
            }
            $purchaseInvoice->delete();
        });
        return back()->with('success', 'Facture supprimée, stock ajusté');
    }
}

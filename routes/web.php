<?php

use App\Http\Controllers\AnnualReviewController;
use App\Http\Controllers\CashSheetController;
use App\Http\Controllers\CleaningTaskController;
use App\Http\Controllers\CompanyInfoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HygieneController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierProductController;
use App\Http\Controllers\TemperatureLogController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profil (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ---------- Inventaire ----------
    Route::get('/inventory', [ProductController::class, 'index'])->name('products.index');
    Route::post('/inventory', [ProductController::class, 'store'])->name('products.store');
    Route::put('/inventory/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/inventory/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    // Sorties / entrées manuelles de stock
    Route::post('/inventory/{product}/stock-out', [StockMovementController::class, 'out'])->name('stock.out');
    Route::post('/inventory/{product}/stock-in', [StockMovementController::class, 'in'])->name('stock.in');

    // ---------- Planning ----------
    Route::get('/planning', [ShiftController::class, 'index'])->name('planning.index');
    Route::post('/planning/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::delete('/planning/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    Route::post('/planning/shifts', [ShiftController::class, 'store'])->name('shifts.store');
    Route::delete('/planning/shifts/{shift}', [ShiftController::class, 'destroy'])->name('shifts.destroy');

    // ---------- Hygiène ----------
    Route::get('/hygiene', [HygieneController::class, 'index'])->name('hygiene.index');
    Route::post('/hygiene/temperatures', [TemperatureLogController::class, 'store'])->name('temperatures.store');
    Route::delete('/hygiene/temperatures/{temperatureLog}', [TemperatureLogController::class, 'destroy'])->name('temperatures.destroy');
    Route::post('/hygiene/cleaning-tasks', [CleaningTaskController::class, 'store'])->name('cleaning-tasks.store');
    Route::post('/hygiene/cleaning-tasks/{cleaningTask}/done', [CleaningTaskController::class, 'markDone'])->name('cleaning-tasks.done');
    Route::delete('/hygiene/cleaning-tasks/{cleaningTask}', [CleaningTaskController::class, 'destroy'])->name('cleaning-tasks.destroy');
    Route::post('/hygiene/deliveries', [DeliveryController::class, 'store'])->name('deliveries.store');
    Route::delete('/hygiene/deliveries/{delivery}', [DeliveryController::class, 'destroy'])->name('deliveries.destroy');

    // ---------- Achat / Fournisseurs / Cadenciers / Factures ----------
    Route::get('/achat', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/achat/fournisseurs/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
    Route::post('/achat/fournisseurs', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::put('/achat/fournisseurs/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/achat/fournisseurs/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

    Route::post('/achat/fournisseurs/{supplier}/cadencier', [SupplierProductController::class, 'store'])->name('cadencier.store');
    Route::put('/achat/cadencier/{cadencier}', [SupplierProductController::class, 'update'])->name('cadencier.update');
    Route::delete('/achat/cadencier/{cadencier}', [SupplierProductController::class, 'destroy'])->name('cadencier.destroy');

    Route::get('/achat/factures', [PurchaseInvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/achat/factures/nouvelle', [PurchaseInvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/achat/factures', [PurchaseInvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/achat/factures/{purchaseInvoice}', [PurchaseInvoiceController::class, 'show'])->name('invoices.show');
    Route::delete('/achat/factures/{purchaseInvoice}', [PurchaseInvoiceController::class, 'destroy'])->name('invoices.destroy');

    // ---------- Caisse ----------
    Route::get('/caisse', [CashSheetController::class, 'index'])->name('cash.index');
    Route::post('/caisse', [CashSheetController::class, 'store'])->name('cash.store');
    Route::put('/caisse/{cashSheet}', [CashSheetController::class, 'update'])->name('cash.update');
    Route::delete('/caisse/{cashSheet}', [CashSheetController::class, 'destroy'])->name('cash.destroy');

    // ---------- Entretiens annuels ----------
    Route::get('/entretiens', [AnnualReviewController::class, 'index'])->name('reviews.index');
    Route::post('/entretiens', [AnnualReviewController::class, 'store'])->name('reviews.store');
    Route::get('/entretiens/{review}', [AnnualReviewController::class, 'show'])->name('reviews.show');
    Route::put('/entretiens/{review}/salarie', [AnnualReviewController::class, 'employeeUpdate'])->name('reviews.employee.update');
    Route::put('/entretiens/{review}/manager', [AnnualReviewController::class, 'managerUpdate'])->name('reviews.manager.update');
    Route::post('/entretiens/{review}/signer', [AnnualReviewController::class, 'sign'])->name('reviews.sign');
    Route::delete('/entretiens/{review}', [AnnualReviewController::class, 'destroy'])->name('reviews.destroy');

    // ---------- Outils (société + documents) ----------
    Route::get('/outils', [DocumentController::class, 'index'])->name('tools.index');
    Route::put('/outils/societe', [CompanyInfoController::class, 'update'])->name('company.update');
    Route::post('/outils/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::put('/outils/documents/{document}', [DocumentController::class, 'update'])->name('documents.update');
    Route::get('/outils/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::delete('/outils/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
});

require __DIR__.'/auth.php';

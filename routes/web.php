<?php

use App\Http\Controllers\AnnualReviewController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profil (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ---------- Entretiens annuels ----------
    Route::get('/entretiens', [AnnualReviewController::class, 'index'])->name('reviews.index');
    Route::post('/entretiens', [AnnualReviewController::class, 'store'])->name('reviews.store');
    Route::get('/entretiens/{review}', [AnnualReviewController::class, 'show'])->name('reviews.show');
    Route::put('/entretiens/{review}/salarie', [AnnualReviewController::class, 'employeeUpdate'])->name('reviews.employee.update');
    Route::put('/entretiens/{review}/manager', [AnnualReviewController::class, 'managerUpdate'])->name('reviews.manager.update');
    Route::post('/entretiens/{review}/signer', [AnnualReviewController::class, 'sign'])->name('reviews.sign');
    Route::delete('/entretiens/{review}', [AnnualReviewController::class, 'destroy'])->name('reviews.destroy');

    // ---------- Équipe (admin) ----------
    Route::get('/equipe', [TeamController::class, 'index'])->name('team.index');
    Route::post('/equipe', [TeamController::class, 'store'])->name('team.store');
    Route::put('/equipe/{user}', [TeamController::class, 'update'])->name('team.update');
    Route::delete('/equipe/{user}', [TeamController::class, 'destroy'])->name('team.destroy');
});

require __DIR__.'/auth.php';

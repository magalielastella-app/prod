<?php

use App\Http\Controllers\CleaningTaskController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HygieneController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\TemperatureLogController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profil (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Inventaire
    Route::get('/inventory', [ProductController::class, 'index'])->name('products.index');
    Route::post('/inventory', [ProductController::class, 'store'])->name('products.store');
    Route::put('/inventory/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/inventory/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Planning
    Route::get('/planning', [ShiftController::class, 'index'])->name('planning.index');
    Route::post('/planning/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::delete('/planning/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    Route::post('/planning/shifts', [ShiftController::class, 'store'])->name('shifts.store');
    Route::delete('/planning/shifts/{shift}', [ShiftController::class, 'destroy'])->name('shifts.destroy');

    // Hygiène
    Route::get('/hygiene', [HygieneController::class, 'index'])->name('hygiene.index');
    Route::post('/hygiene/temperatures', [TemperatureLogController::class, 'store'])->name('temperatures.store');
    Route::delete('/hygiene/temperatures/{temperatureLog}', [TemperatureLogController::class, 'destroy'])->name('temperatures.destroy');
    Route::post('/hygiene/cleaning-tasks', [CleaningTaskController::class, 'store'])->name('cleaning-tasks.store');
    Route::post('/hygiene/cleaning-tasks/{cleaningTask}/done', [CleaningTaskController::class, 'markDone'])->name('cleaning-tasks.done');
    Route::delete('/hygiene/cleaning-tasks/{cleaningTask}', [CleaningTaskController::class, 'destroy'])->name('cleaning-tasks.destroy');
    Route::post('/hygiene/deliveries', [DeliveryController::class, 'store'])->name('deliveries.store');
    Route::delete('/hygiene/deliveries/{delivery}', [DeliveryController::class, 'destroy'])->name('deliveries.destroy');
});

require __DIR__.'/auth.php';

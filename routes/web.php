<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\FnbController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->role === 'admin' ? redirect()->route('dashboard') : redirect()->route('rentals.index');
    }
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified', 'is_admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('devices', DeviceController::class);
    Route::resource('products', ProductController::class)->except(['create', 'show', 'edit']);

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/promo', [ReportController::class, 'updatePromo'])->name('reports.promo');

    Route::get('/print/rental/{rental}', [RentalController::class, 'print'])->name('rentals.print');
    Route::get('/print/fnb/{fnbTransaction}', [FnbController::class, 'print'])->name('fnb.print');

    Route::patch('/reservations/{reservation}/status', [ReservationController::class, 'updateStatus'])->name('reservations.status');
});

Route::middleware('auth')->group(function () {
    Route::resource('rentals', RentalController::class);
    Route::resource('reservations', ReservationController::class)->except(['create', 'show', 'edit', 'update']);
    
    Route::get('/fnb', [FnbController::class, 'index'])->name('fnb.index');
    Route::post('/fnb', [FnbController::class, 'store'])->name('fnb.store');
    Route::patch('/fnb/{fnbTransaction}/complete', [FnbController::class, 'markCompleted'])->name('fnb.complete');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

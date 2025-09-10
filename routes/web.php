<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PackController;


Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');






Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [OrderController::class, 'dashboard'])->name('dashboard');

    // Order Management
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::post('/orders/{id}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');

    Route::get('/orders/{id}/pdf', [OrderController::class, 'downloadPdf'])->name('orders.pdf');


    // Pack Management
    Route::get('/packs', [PackController::class, 'index'])->name('packs.index');
    Route::get('/packs/create', [PackController::class, 'create'])->name('packs.create');
    Route::post('/packs', [PackController::class, 'store'])->name('packs.store');
    Route::delete('/packs/{id}', [PackController::class, 'destroy'])->name('packs.destroy');

    // Settings
    Route::get('/setting', [OrderController::class, 'setting'])->name('settings');
    Route::post('/setting', [OrderController::class, 'storeOrUpdate'])->name('settings.storeOrUpdate');

});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

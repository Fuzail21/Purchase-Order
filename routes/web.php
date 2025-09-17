<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PackController;
use App\Http\Controllers\SizeGroupController;


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
    Route::get('/orders/pdf/{id}', [OrderController::class, 'downloadPdf'])->name('orders.pdf');
    Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');



    // Pack Management
    Route::get('/packs', [PackController::class, 'index'])->name('packs.index');
    Route::post('/packs', [PackController::class, 'store'])->name('packs.store');
    Route::delete('/packs/{id}', [PackController::class, 'destroy'])->name('packs.destroy');

    // Size Group Management
    Route::get('/size', [SizeGroupController::class, 'index'])->name('size.index');
    Route::post('/size', [SizeGroupController::class, 'store'])->name('size.store');
    Route::delete('/size/{id}', [SizeGroupController::class, 'destroy'])->name('size.destroy');

    // Settings
    Route::get('/setting', [OrderController::class, 'setting'])->name('settings');
    Route::post('/setting', [OrderController::class, 'storeOrUpdate'])->name('settings.storeOrUpdate');

});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

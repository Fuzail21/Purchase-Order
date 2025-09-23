<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PackController;
use App\Http\Controllers\SizeGroupController;
use App\Http\Controllers\AddOnController;


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
    Route::put('/orders/{id}', [OrderController::class, 'update'])->name('orders.update');
    Route::get('/orders/pdf/{id}', [OrderController::class, 'downloadPdf'])->name('orders.pdf');
    Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');



    // Pack Management
    Route::get('/packs/list', [PackController::class, 'index'])->name('packs.index');
    Route::get('/packs/create', [PackController::class, 'create'])->name('packs.create');
    Route::post('/packs', [PackController::class, 'store'])->name('packs.store');    
    Route::get('/packs/{id}/edit', [PackController::class, 'edit'])->name('packs.edit');
    Route::put('/packs/{id}', [PackController::class, 'update'])->name('packs.update');
    Route::delete('/packs/{id}', [PackController::class, 'destroy'])->name('packs.destroy');

    // Size Group Management
    Route::get('/size', [SizeGroupController::class, 'index'])->name('size.index');
    Route::post('/size', [SizeGroupController::class, 'store'])->name('size.store');
    Route::delete('/size/{id}', [SizeGroupController::class, 'destroy'])->name('size.destroy');

    Route::get('/add-on', [AddOnController::class, 'index'])->name('addOn.index');
    Route::post('/add-on', [AddOnController::class, 'store'])->name('addOn.store');
    Route::delete('/add-on/{id}', [AddOnController::class, 'destroy'])->name('addOn.destroy');

    // Settings
    Route::get('/setting', [OrderController::class, 'setting'])->name('settings');
    Route::post('/setting', [OrderController::class, 'storeOrUpdate'])->name('settings.storeOrUpdate');

});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

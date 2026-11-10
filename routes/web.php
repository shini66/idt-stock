<?php

use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'can:use-stock'])->group(function(){
    Route::get('/stock', [InventoryController::class, 'index'])->name('stock.index');
    Route::get('/stock/live', [InventoryController::class, 'live'])->name('stock.live');
    Route::post('/movements', [MovementController::class, 'store'])->name('movements.store');
    Route::middleware('can:manage-products')->group(function() {
        Route::resource('products', ProductController::class)
            ->only(['create', 'store', 'edit', 'update', 'destroy']);
    });
});

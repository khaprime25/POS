<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\KitchenController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('categories', CategoryController::class);
Route::resource('products', ProductController::class);
Route::resource('variants', ProductVariantController::class);

Route::get('/pos', [POSController::class, 'index'])->middleware(['auth', 'verified'])->name('pos.index');
Route::post('/pos/cart/add', [PosController::class, 'addToCart'])->name('pos.cart.add');
Route::post('/pos/cart/increase/{variantId}', [POSController::class, 'increaseQty'])->name('pos.cart.increase');
Route::post('/pos/cart/decrease/{variantId}', [POSController::class, 'decreaseQty'])->name('pos.cart.decrease');
Route::post('/pos/cart/remove/{variantId}', [POSController::class, 'removeItem'])->name('pos.cart.remove');

Route::post('/sales/store', [SaleController::class, 'storeSale'])->name('sales.store');

Route::get('/kitchen', [KitchenController::class, 'index'])->name('kitchen.index');

require __DIR__ . '/auth.php';

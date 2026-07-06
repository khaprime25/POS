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
use Illuminate\Support\Facades\Auth;


Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    return match (Auth::user()->role) {
        'owner' => redirect()->route('dashboard'),
        'cashier' => redirect()->route('pos.index'),
        'chef' => redirect()->route('kitchen.index'),
        default => abort(403),
    };
});

Route::middleware(['auth', 'verified', 'role:owner'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('variants', ProductVariantController::class);
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
});

Route::middleware(['auth', 'role:owner,cashier'])->group(function () {
    Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
    Route::post('/pos/cart/add', [POSController::class, 'addToCart'])->name('pos.cart.add');
    Route::post('/pos/cart/increase/{variantId}', [POSController::class, 'increaseQty'])->name('pos.cart.increase');
    Route::post('/pos/cart/decrease/{variantId}', [POSController::class, 'decreaseQty'])->name('pos.cart.decrease');
    Route::post('/pos/cart/remove/{variantId}', [POSController::class, 'removeItem'])->name('pos.cart.remove');
    Route::post('/sales/store', [SaleController::class, 'storeSale'])->name('sales.store');
});

Route::middleware(['auth', 'role:owner,chef'])->group(function () {
    Route::get('/kitchen', [KitchenController::class, 'index'])->name('kitchen.index');
    Route::post('/kitchen/{sale}/status', [KitchenController::class, 'updateStatus'])->name('kitchen.status');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

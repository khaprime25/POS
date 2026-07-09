<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProductModifierController;
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

    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
    Route::post('/user/{user}/toggle', [UserController::class, 'toggle'])->name('user.toggle');

    Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
    Route::post('/settings/tax', [SettingController::class, 'updateTax'])->name('setting.tax.update');
    Route::post('/settings/discount', [SettingController::class, 'updateDiscount'])->name('setting.discount.update');

    Route::post('/reports/{report}/resolve', [ReportController::class, 'resolve'])->name('reports.resolve');
    Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');

    Route::get('/modifiers', [ProductModifierController::class, 'index'])->name('modifiers.index');
    Route::post('/modifiers/store', [ProductModifierController::class, 'store'])->name('modifiers.store');
    Route::get('/modifiers/{modifier}/edit', [ProductModifierController::class, 'edit'])->name('modifiers.edit');
    Route::put('/modifiers/{modifier}', [ProductModifierController::class, 'update'])->name('modifiers.update');
    Route::delete('/modifiers/{modifier}', [ProductModifierController::class, 'destroy'])->name('modifiers.destroy');
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

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/store', [ReportController::class, 'store'])->name('reports.store');
});

require __DIR__ . '/auth.php';

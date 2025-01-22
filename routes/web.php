<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderManagementController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\InventoryController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/filter', [ProductController::class, 'filter'])->name('products.filter');

// Trasy logowania
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

//rejestracja

Route::get('/register', [RegistrationController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegistrationController::class, 'register']);

// Trasa wylogowania
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/products');
})->name('logout')->middleware('auth'); // Dodano middleware auth

//trasy koszyka 

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');

//zamowienia

Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');

Route::patch('/orders/{order}/update-payment', [OrderController::class, 'updatePaymentStatus'])->name('orders.updatePaymentStatus');
Route::patch('/orders/{order}/update-shipment', [OrderController::class, 'updateShipmentStatus'])->name('orders.updateShipmentStatus');
Route::get('/orders/{order}/pay', [OrderController::class, 'showPayment'])->name('orders.pay');
Route::post('/orders/{order}/pay', [OrderController::class, 'processPayment'])->name('orders.processPayment');
Route::delete('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');



//podsumowanie zamowienia 

Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
});

//panel pracowniczy(zarzadzanie zamowieniami)


Route::middleware(['auth'])->group(function () {
    Route::get('/worker/orders', [OrderManagementController::class, 'index'])->name('worker.orders.index');
    Route::patch('/worker/orders/{order}/shipment', [OrderManagementController::class, 'updateShipmentStatus'])->name('worker.orders.updateShipment');
    Route::get('/worker/orders/{order}/edit', [OrderManagementController::class, 'edit'])->name('worker.orders.edit');
    Route::patch('/worker/orders/{order}', [OrderManagementController::class, 'update'])->name('worker.orders.update');
    Route::delete('/worker/orders/{order}', [OrderManagementController::class, 'destroy'])->name('worker.orders.destroy');
    Route::patch('/worker/orders/{order}/shipment-status', [OrderManagementController::class, 'updateShipmentStatus'])
    ->name('worker.orders.updateShipmentStatus');
});

//admin


Route::middleware('auth')->group(function () {
    Route::get('/admin/users', [AdminController::class, 'index'])->name('admin.users.index');
    Route::patch('/admin/users/{user}/role', [AdminController::class, 'updateRole'])->name('admin.users.updateRole');
    Route::delete('/admin/users/{user}', [AdminController::class, 'destroy'])->name('admin.users.destroy');
});

//pojedynczy produkt
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');


//favourites



Route::middleware(['auth'])->group(function () {
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{product}', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/favorites/{product}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
});


//przeglad magazynu 

Route::middleware(['auth'])->group(function () {
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::get('/inventory/{product}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
    Route::patch('/inventory/{product}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/inventory/{product}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
});






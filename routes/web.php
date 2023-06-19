<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Admin
Route::get('/admin', [AdminController::class, 'home'])->name('admin-home');
Route::get('/create-user', [AdminController::class, 'createUser'])->name('create-user');
Route::post('/create-user', [AdminController::class, 'createUserSubmit'])->name('create-user-submit');
Route::get('/edit-user/{id}', [AdminController::class, 'editUser'])->name('edit-user');
Route::post('/edit-user/{id}', [AdminController::class, 'editUserSubmit'])->name('edit-user-submit');
Route::get('/delete-user/{id?}', [AdminController::class, 'deleteUser'])->name('delete-user');

// Frontend
Route::middleware('guest')->group(function() {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'submit'])->name('login-submit');
});

Route::middleware('auth')->group(function() {
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    
    Route::get('/driver', [DriverController::class, 'customerList'])->name('customer-list');
    Route::get('/driver/start-trip', [DriverController::class, 'startTrip'])->name('start-trip');
    
    Route::get('/customer-info/{id}', [CustomerController::class, 'index'])->name('info');
    Route::get('/select-product', [CustomerController::class, 'selectProduct'])->name('select-product');
    Route::post('/select-product', [CustomerController::class, 'selectProductSubmit'])->name('select-product-submit');
    
    Route::get('/cart', [OrderController::class, 'cart'])->name('cart');
    Route::post('/cart', [OrderController::class, 'cartSubmit'])->name('cart-submit');
    Route::get('/order-receipt', [OrderController::class, 'orderReceipt'])->name('order-receipt');
});
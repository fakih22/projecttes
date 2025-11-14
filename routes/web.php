<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DonatController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UserAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;



Route::post('/midtrans/notification', [MidtransController::class, 'notification']);


Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/profile', 'ProfileController@index')->name('profile');
Route::put('/profile', 'ProfileController@update')->name('profile.update');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::middleware('auth')->group(function () {
    Route::resource('user', UserController::class);
});

Route::post('/midtrans/create-transaction', [MidtransController::class, 'createTransaction']);
Route::post('/midtrans/notification', [MidtransController::class, 'handleNotification']);
Route::get('/transactions', [MidtransController::class, 'getTransactions']);



// ROUTE HALAMAN KATEGORI DAN YAP DONAT AJAH
Route::get('/category', [CategoryController::class, 'index'])->name('category.index');
Route::get('/donat', [DonatController::class, 'index'])->name('donat.index');

//ROUTE CUSTOMER BROOOOOOOOOOOOOOOOOOOOO 
Route::get('/customer', [CustomerController::class, 'index'])->name('customer.index');
Route::get('/customer/create', [CustomerController::class, 'create'])->name('customer.create');
Route::post('/customer', [CustomerController::class, 'store'])->name('customer.store');
Route::get('/customer/{customer}/edit', [CustomerController::class, 'edit'])->name('customer.edit');
Route::put('/customer/{customer}', [CustomerController::class, 'update'])->name('customer.update');
Route::delete('/customer/{customer}', [CustomerController::class, 'destroy'])->name('customer.destroy');

// ROUTE: KATEGORI MAS
Route::get('/category', [CategoryController::class, 'index'])->name('category.index');
Route::get('/category/create', [CategoryController::class, 'create'])->name('category.create');
Route::post('/category', [CategoryController::class, 'store'])->name('category.store');
Route::get('/category/{category}/edit', [CategoryController::class, 'edit'])->name('category.edit');
Route::put('/category/{category}', [CategoryController::class, 'update'])->name('category.update');
Route::delete('/category/{category}', [CategoryController::class, 'destroy'])->name('category.destroy');

// ROUTE BUAT DONAT MBAK
Route::get('/donat', [DonatController::class, 'index'])->name('donat.index');
Route::get('/donat/create', [DonatController::class, 'create'])->name('donat.create');
Route::post('/donat', [DonatController::class, 'store'])->name('donat.store');
Route::get('/donat/{donat}/edit', [DonatController::class, 'edit'])->name('donat.edit');
Route::put('/donat/{donat}', [DonatController::class, 'update'])->name('donat.update');
Route::delete('/donat/{donat}', [DonatController::class, 'destroy'])->name('donat.destroy');

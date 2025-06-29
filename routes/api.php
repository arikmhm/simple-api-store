<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;

//  Rute Autentikasi Publik 
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

//  Rute Autentikasi Umum 
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // Rute Pesanan untuk Customer (membuat order, melihat order sendiri)
    Route::post('/orders', [OrderController::class, 'store']); // Customer membuat order
    Route::get('/orders/user', [OrderController::class, 'userOrders']); // Customer melihat ordernya sendiri
});

//  Rute Produk Publik (READ-ONLY) 
// Rute untuk melihat daftar produk dan detail produk tanpa perlu login
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);


//  Rute ADMIN (Auth:sanctum DAN Admin Role) 
// Semua rute di sini membutuhkan user terautentikasi DAN memiliki role 'admin'
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // Rute Produk untuk Admin (CREATE, UPDATE, DELETE)
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::patch('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);

    // Rute Pesanan untuk Admin (melihat semua, update status, delete)
    Route::get('/orders', [OrderController::class, 'index']); // Admin melihat semua order
    Route::patch('/orders/{order}', [OrderController::class, 'update']); // Admin update status order
    Route::delete('/orders/{order}', [OrderController::class, 'destroy']); // Admin delete order

});


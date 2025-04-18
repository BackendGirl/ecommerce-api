<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;

//auth routes
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [LoginController::class, 'register']);

//get all products
Route::get('/allProducts', [ProductController::class, 'index']);

//admin accesible routes
Route::middleware(['auth:sanctum', 'verify.admin'])->group(function () {

    //create new product
    Route::post('/storeProducts', [ProductController::class, 'store']);

    //update the existing product
    Route::post('/updateProducts/{id}', [ProductController::class, 'update']);

    //delete product
    Route::post('/deleteProducts/{id}', [ProductController::class, 'delete']);
});
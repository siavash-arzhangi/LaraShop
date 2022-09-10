<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/user/register', [AuthController::class, 'register'])->name('register');
Route::post('/user/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:api')->controller(AuthController::class)->group(function () {
    Route::get('/users', 'index');
    Route::post('/user/create', 'create');
    Route::post('/user/read', 'read');
    Route::post('/user/update', 'update'); // TODO
    Route::post('/user/delete', 'delete');
    Route::post('/user/logout', 'logout');
});

Route::middleware('auth:api')->controller(ProductController::class)->group(function () {
    Route::get('/products', 'index');
    Route::post('/product/create', 'create');
    Route::post('/product/read', 'read');
    Route::post('/product/update', 'update');
    Route::post('/product/delete', 'delete');
});

Route::middleware('auth:api')->controller(CategoryController::class)->group(function () {
    Route::get('/categories', 'index');
    Route::post('/category/create', 'create');
    Route::post('/category/read', 'read');
    Route::post('/category/update', 'update');
    Route::post('/category/delete', 'delete');
});

Route::middleware('auth:api')->controller(DiscountController::class)->group(function () {
    Route::get('/discounts', 'index');
    Route::post('/discount/create', 'create');
    Route::post('/discount/read', 'read');
    Route::post('/discount/update', 'update');
    Route::post('/discount/delete', 'delete');
});

Route::middleware('auth:api')->controller(InvoiceController::class)->group(function () {
    Route::get('/invoices', 'index');
    Route::post('/invoice/create', 'create');
    Route::post('/invoice/read', 'read');
    Route::post('/invoice/update', 'update'); // TODO
    Route::post('/invoice/delete', 'delete');
    Route::post('/invoice/pay', 'pay');
    Route::get('/invoice/verify', 'verify');
});

Route::middleware('auth:api')->controller(OrderController::class)->group(function () {
    Route::get('/orders', 'index');
    Route::post('/order/create', 'create'); // TODO
    Route::post('/order/read', 'read');
    Route::post('/order/update', 'update');
    Route::post('/order/delete', 'delete'); // TODO
});
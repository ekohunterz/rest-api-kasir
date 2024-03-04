<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user()->load('roles');
});


Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::apiResource('products', ProductController::class);
    Route::apiResource('/categories', CategoryController::class);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);

    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/export-yearly', [ReportController::class, 'exportYearlyReportToExcel']);
    Route::get('/export-monthly', [ReportController::class, 'exportMonthlyReportToExcel']);

    Route::apiResource('/users', UserController::class);
    Route::put('/update-profile', [UserController::class, 'updateProfile']);
});



// Route::get('/carts', [CartController::class, 'index']);

// Route::post('/carts', [CartController::class, 'store']);

// Route::put('/carts/{id}', [CartController::class, 'update']);
// Route::post('/carts/delete', [CartController::class, 'destroy']);
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\CollectionController;
use App\Http\Controllers\Api\ReturnController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\ReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// مسارات المصادقة (بدون middleware)
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// مسارات محمية بـ Sanctum
Route::middleware('auth:sanctum')->group(function () {
    
    // مسارات المصادقة المحمية
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('profile', [AuthController::class, 'profile']);
        Route::put('profile', [AuthController::class, 'updateProfile']);
    });

    // مسارات الطلبات
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'store']);
        Route::get('/{id}', [OrderController::class, 'show']);
        Route::put('/{id}/status', [OrderController::class, 'updateStatus']);
        Route::post('/{id}/repeat', [OrderController::class, 'repeatOrder']);
    });

    // مسارات العناصر (الأدوية)
    Route::prefix('items')->group(function () {
        Route::get('/', [ItemController::class, 'index']);
        Route::post('/', [ItemController::class, 'store']);
        Route::get('/{id}', [ItemController::class, 'show']);
        Route::put('/{id}', [ItemController::class, 'update']);
        Route::delete('/{id}', [ItemController::class, 'destroy']);
        Route::get('/search/{query}', [ItemController::class, 'search']);
    });

    // مسارات الفواتير
    Route::prefix('invoices')->group(function () {
        Route::get('/', [InvoiceController::class, 'index']);
        Route::get('/{id}', [InvoiceController::class, 'show']);
        Route::put('/{id}', [InvoiceController::class, 'update']);
    });

    // مسارات التحصيلات
    Route::prefix('collections')->group(function () {
        Route::get('/', [CollectionController::class, 'index']);
        Route::post('/', [CollectionController::class, 'store']);
        Route::get('/{id}', [CollectionController::class, 'show']);
        Route::put('/{id}', [CollectionController::class, 'update']);
        Route::delete('/{id}', [CollectionController::class, 'destroy']);
    });

    // مسارات المرتجعات
    Route::prefix('returns')->group(function () {
        Route::get('/', [ReturnController::class, 'index']);
        Route::post('/', [ReturnController::class, 'store']);
        Route::get('/{id}', [ReturnController::class, 'show']);
        Route::put('/{id}/status', [ReturnController::class, 'updateStatus']);
    });

    // مسارات الموردين
    Route::prefix('suppliers')->group(function () {
        Route::get('/', [SupplierController::class, 'index']);
        Route::post('/', [SupplierController::class, 'store']);
        Route::get('/{id}', [SupplierController::class, 'show']);
        Route::put('/{id}', [SupplierController::class, 'update']);
        Route::delete('/{id}', [SupplierController::class, 'destroy']);
    });

    // مسارات التقارير
    Route::prefix('reports')->group(function () {
        Route::get('dashboard-stats', [ReportController::class, 'dashboardStats']);
        Route::get('orders', [ReportController::class, 'ordersReport']);
        Route::get('invoices', [ReportController::class, 'invoicesReport']);
        Route::get('collections', [ReportController::class, 'collectionsReport']);
        Route::get('financial', [ReportController::class, 'financialReport']);
    });
});

// مسار للحصول على معلومات المستخدم المصادق عليه
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

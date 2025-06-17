<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\ItemController;
use App\Http\Controllers\Web\InvoiceController;
use App\Http\Controllers\Web\CollectionController;
use App\Http\Controllers\Web\SupplierController;
use App\Http\Controllers\Web\ReturnController;
use App\Http\Controllers\Web\ReportController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\WarehouseController;

// إعادة توجيه الصفحة الرئيسية إلى تسجيل الدخول
Route::get('/', function () {
    return redirect()->route('login');
});

// مسارات المصادقة
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// مسارات محمية
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // مسارات الطلبات
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/create', [OrderController::class, 'create'])->name('create');
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::get('/{id}', [OrderController::class, 'show'])->name('show');
        Route::put('/{id}/status', [OrderController::class, 'updateStatus'])->name('updateStatus');
        Route::get('/{id}/repeat', [OrderController::class, 'repeat'])->name('repeat');
    });

    // مسارات العناصر
    Route::prefix('items')->name('items.')->group(function () {
        Route::get('/', [ItemController::class, 'index'])->name('index');
        Route::get('/create', [ItemController::class, 'create'])->name('create');
        Route::post('/', [ItemController::class, 'store'])->name('store');
        Route::get('/low-stock', [ItemController::class, 'lowStock'])->name('lowStock');
        Route::get('/search', [ItemController::class, 'search'])->name('search');
        Route::get('/import/form', [ItemController::class, 'importForm'])->name('import.form');
        Route::post('/import', [ItemController::class, 'import'])->name('import');
        Route::get('/sample/download', [ItemController::class, 'downloadSample'])->name('sample');
        Route::get('/{id}', [ItemController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ItemController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ItemController::class, 'update'])->name('update');
        Route::delete('/{id}', [ItemController::class, 'destroy'])->name('destroy');
    });

    // مسارات الفواتير
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/pending', [InvoiceController::class, 'pending'])->name('pending');
        Route::get('/overdue', [InvoiceController::class, 'overdue'])->name('overdue');
        Route::get('/{id}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [InvoiceController::class, 'edit'])->name('edit');
        Route::put('/{id}', [InvoiceController::class, 'update'])->name('update');
        Route::post('/{id}/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('markAsPaid');
        Route::get('/{id}/print', [InvoiceController::class, 'print'])->name('print');
    });

    // مسارات التحصيلات
    Route::prefix('collections')->name('collections.')->group(function () {
        Route::get('/', [CollectionController::class, 'index'])->name('index');
        Route::get('/create', [CollectionController::class, 'create'])->name('create');
        Route::post('/', [CollectionController::class, 'store'])->name('store');
        Route::get('/{id}', [CollectionController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [CollectionController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CollectionController::class, 'update'])->name('update');
        Route::delete('/{id}', [CollectionController::class, 'destroy'])->name('destroy');
        Route::get('/invoice/{invoiceId}/details', [CollectionController::class, 'getInvoiceDetails'])->name('invoiceDetails');
    });

    // مسارات الموردين
    Route::prefix('suppliers')->name('suppliers.')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('index');
        Route::get('/create', [SupplierController::class, 'create'])->name('create');
        Route::post('/', [SupplierController::class, 'store'])->name('store');
        Route::get('/search', [SupplierController::class, 'search'])->name('search');
        Route::get('/import/form', [SupplierController::class, 'importForm'])->name('import.form');
        Route::post('/import', [SupplierController::class, 'import'])->name('import');
        Route::get('/sample/download', [SupplierController::class, 'downloadSample'])->name('sample');
        Route::get('/{id}', [SupplierController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [SupplierController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SupplierController::class, 'update'])->name('update');
        Route::delete('/{id}', [SupplierController::class, 'destroy'])->name('destroy');
    });

    // مسارات المستخدمين
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/search', [UserController::class, 'search'])->name('search');
        Route::get('/{id}', [UserController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
    });

    // مسارات المخازن
    Route::prefix('warehouses')->name('warehouses.')->group(function () {
        Route::get('/', [WarehouseController::class, 'index'])->name('index');
        Route::get('/create', [WarehouseController::class, 'create'])->name('create');
        Route::post('/', [WarehouseController::class, 'store'])->name('store');
        Route::get('/{id}', [WarehouseController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [WarehouseController::class, 'edit'])->name('edit');
        Route::put('/{id}', [WarehouseController::class, 'update'])->name('update');
        Route::delete('/{id}', [WarehouseController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/items', [WarehouseController::class, 'items'])->name('items');
        Route::get('/{id}/reports', [WarehouseController::class, 'reports'])->name('reports');
    });

    // مسارات التقارير
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
        Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
        Route::get('/top-items', [ReportController::class, 'topItems'])->name('topItems');
        Route::post('/sales/export', [ReportController::class, 'exportSales'])->name('exportSales');
        Route::post('/financial/export', [ReportController::class, 'exportFinancial'])->name('exportFinancial');
        Route::post('/inventory/export', [ReportController::class, 'exportInventory'])->name('exportInventory');
    });
});

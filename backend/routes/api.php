<?php

use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\KardexController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\SupplierController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth:api')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::post('logout', [AuthController::class, 'logout']);
    });

    Route::get('dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:dashboard.view');

    Route::apiResource('products', ProductController::class)
        ->middleware('permission:products.view');

    Route::apiResource('customers', CustomerController::class)
        ->middleware('permission:customers.view');

    Route::apiResource('suppliers', SupplierController::class)
        ->middleware('permission:suppliers.view');

    Route::get('sales', [SaleController::class, 'index'])->middleware('permission:sales.view');
    Route::post('sales', [SaleController::class, 'store'])->middleware('permission:sales.create');
    Route::get('sales/{sale}', [SaleController::class, 'show'])->middleware('permission:sales.view');
    Route::post('sales/{sale}/confirm', [SaleController::class, 'confirm'])->middleware('permission:sales.confirm');

    Route::get('purchases', [PurchaseController::class, 'index'])->middleware('permission:purchases.view');
    Route::post('purchases', [PurchaseController::class, 'store'])->middleware('permission:purchases.create');
    Route::get('purchases/{purchase}', [PurchaseController::class, 'show'])->middleware('permission:purchases.view');
    Route::post('purchases/{purchase}/receive', [PurchaseController::class, 'receive'])->middleware('permission:purchases.receive');

    Route::get('kardex', [KardexController::class, 'index'])->middleware('permission:inventory.view');

    Route::prefix('reports')->middleware('permission:reports.view')->group(function () {
        Route::get('sales', [ReportController::class, 'sales']);
        Route::get('sales/excel', [ReportController::class, 'salesExcel']);
        Route::get('sales/pdf', [ReportController::class, 'salesPdf']);
    });

    Route::get('activity-logs', [ActivityLogController::class, 'index'])
        ->middleware('permission:activity.view');

    Route::prefix('roles')->middleware('permission:roles.manage')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::get('/permissions', [RoleController::class, 'permissions']);
        Route::post('/', [RoleController::class, 'store']);
        Route::put('/{role}', [RoleController::class, 'update']);
    });
});

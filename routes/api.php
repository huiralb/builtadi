<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\TransactionSummaryController;
use App\Http\Controllers\API\V1\SalesAnalyticsController;
use App\Http\Controllers\Api\V1\SalesPerformanceController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\API\V1\SalesOrderController;

Route::prefix('v1')->group(function () {
    Route::post('customers', [CustomerController::class, 'store']);
    Route::put('customers/{customer}', [CustomerController::class, 'update']);
    Route::get('sales/summary', [TransactionSummaryController::class, 'index']);
    Route::get('sales/target', [SalesAnalyticsController::class, 'index']);
    Route::get('sales/performance', [SalesPerformanceController::class, 'index']);
    Route::post('sales-orders', [SalesOrderController::class, 'store']);
});

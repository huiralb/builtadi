<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\TransactionSummaryController;
use App\Http\Controllers\API\V1\SalesAnalyticsController;

Route::prefix('v1')->group(function () {
    Route::get('transaction-summary', [TransactionSummaryController::class, 'index']);
    Route::get('sales/target', [SalesAnalyticsController::class, 'index']);
});

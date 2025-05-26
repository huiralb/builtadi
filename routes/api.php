<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\TransactionSummaryController;

Route::prefix('v1')->group(function () {
    Route::get('transaction-summary', [TransactionSummaryController::class, 'index']);
});

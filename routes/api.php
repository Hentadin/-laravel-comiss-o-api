<?php

use App\Http\Controllers\Api\SaleController;
use Illuminate\Support\Facades\Route;


Route::prefix('sales')->group(function () {
    Route::get('/', [SaleController::class, 'index']);
    Route::post('/', [SaleController::class, 'store']);
    Route::delete('/{id}', [SaleController::class, 'destroy']);
});
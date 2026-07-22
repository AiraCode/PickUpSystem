<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\AccuController;
use App\Http\Controllers\Api\Admin\BankController;
use App\Http\Controllers\Api\Admin\CityAccuPriceController;
use App\Http\Controllers\Api\Admin\CityController;
use App\Http\Controllers\Api\Admin\ReceiptController;
use Illuminate\Support\Facades\Route;


Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::prefix('admin')->group(function () {
        Route::apiResource('cities', CityController::class);
        Route::apiResource('accus', AccuController::class);
        Route::get('cities/{cityId}/accus', [CityAccuPriceController::class, 'index']);
        Route::post('cities/{cityId}/accus', [CityAccuPriceController::class, 'store']);
        Route::put('cities/{cityId}/accus/{accuId}', [CityAccuPriceController::class, 'update']);
        Route::delete('cities/{cityId}/accus/{accuId}', [CityAccuPriceController::class, 'destroy']);
        Route::apiResource('banks', BankController::class)->except(['show']);
        Route::get('receipts', [ReceiptController::class, 'index']);
        Route::get('receipts/{id}', [ReceiptController::class, 'show']);
    });
});

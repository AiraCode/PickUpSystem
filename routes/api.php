<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\AccuController;
use App\Http\Controllers\Api\Admin\BankController;
use App\Http\Controllers\Api\Admin\CityAccuPriceController;
use App\Http\Controllers\Api\Admin\CityController;
use App\Http\Controllers\Api\Admin\ReceiptController;
use App\Http\Controllers\Api\Admin\CustomerController;
use App\Http\Controllers\Api\Admin\OrderController;
use App\Http\Controllers\Api\Admin\TransferController;
use App\Http\Controllers\Api\Admin\WarehouseController;
use App\Http\Controllers\Api\Admin\ShipmentController;
use App\Http\Controllers\Api\Admin\DashboardStatsController;
use App\Http\Controllers\Api\Admin\UserController;
use Illuminate\Support\Facades\Route;


Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::prefix('admin')->group(function () {
        Route::get('dashboard-stats', [DashboardStatsController::class, 'index']);
        Route::apiResource('users', UserController::class);

        Route::apiResource('cities', CityController::class);
        Route::apiResource('accus', AccuController::class);
        Route::get('cities/{cityId}/accus', [CityAccuPriceController::class, 'index']);
        Route::post('cities/{cityId}/accus', [CityAccuPriceController::class, 'store']);
        Route::put('cities/{cityId}/accus/{accuId}', [CityAccuPriceController::class, 'update']);
        Route::delete('cities/{cityId}/accus/{accuId}', [CityAccuPriceController::class, 'destroy']);
        Route::apiResource('banks', BankController::class)->except(['show']);
        Route::apiResource('receipts', ReceiptController::class);

        Route::apiResource('customers', CustomerController::class)->only(['index', 'show']);
        Route::put('customers/{id}/flag', [CustomerController::class, 'updateFlag']);
        // Route::post('customers/verify-ktp', [CustomerController::class, 'verifyKtp']);
        Route::apiResource('orders', OrderController::class)->only(['index', 'show']);
        Route::put('orders/{id}/status', [OrderController::class, 'updateStatus']);
        Route::apiResource('transfers', TransferController::class)->only(['index', 'show', 'update']);
        Route::apiResource('storages', WarehouseController::class);
        Route::apiResource('shipments', ShipmentController::class);
    });
});

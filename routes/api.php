<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\AccuController as AdminAccuController;
use App\Http\Controllers\Api\Admin\BankController as AdminBankController;
use App\Http\Controllers\Api\Admin\CityAccuPriceController;
use App\Http\Controllers\Api\Admin\CityController as AdminCityController;
use App\Http\Controllers\Api\Admin\ReceiptController as AdminReceiptController;
use App\Http\Controllers\Api\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\Admin\TransferController;
use App\Http\Controllers\Api\Admin\WarehouseController;
use App\Http\Controllers\Api\Admin\ShipmentController;
use App\Http\Controllers\Api\Admin\DashboardStatsController;
use App\Http\Controllers\Api\Admin\UserController;

// Customer Public API Controllers (Folder: App\Http\Controllers\Api\Customer)
use App\Http\Controllers\Api\Customer\CityController as CustomerCityController;
use App\Http\Controllers\Api\Customer\AccuController as CustomerAccuController;
use App\Http\Controllers\Api\Customer\StorageController as CustomerStorageController;
use App\Http\Controllers\Api\Customer\BankController as CustomerBankController;
use App\Http\Controllers\Api\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Api\Customer\ReceiptController as CustomerReceiptController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CUSTOMER API ROUTES (Public - Tanpa Login)
| Prefix: /api/customer/
|--------------------------------------------------------------------------
*/
Route::prefix('customer')->group(function () {
    Route::get('cities', [CustomerCityController::class, 'index']);
    Route::get('cities/{cityId}/accus', [CustomerAccuController::class, 'getByCity']);
    Route::get('storages', [CustomerStorageController::class, 'index']);
    Route::get('banks', [CustomerBankController::class, 'index']);
    Route::post('orders', [CustomerOrderController::class, 'store']);
    Route::get('orders/{id}', [CustomerOrderController::class, 'show']);
    Route::get('receipts/{orderId}', [CustomerReceiptController::class, 'show']);
});

/*
|--------------------------------------------------------------------------
| ADMIN API ROUTES (Protected - Perlu Login Sanctum)
| Prefix: /api/admin/
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/admin/profile', [AuthController::class, 'updateProfile']);

    Route::prefix('admin')->group(function () {
        Route::get('dashboard-stats', [DashboardStatsController::class, 'index']);
        Route::apiResource('users', UserController::class);

        Route::apiResource('cities', AdminCityController::class);
        Route::apiResource('accus', AdminAccuController::class);
        Route::get('cities/{cityId}/accus', [CityAccuPriceController::class, 'index']);
        Route::post('cities/{cityId}/accus', [CityAccuPriceController::class, 'store']);
        Route::put('cities/{cityId}/accus/{accuId}', [CityAccuPriceController::class, 'update']);
        Route::delete('cities/{cityId}/accus/{accuId}', [CityAccuPriceController::class, 'destroy']);
        Route::apiResource('banks', AdminBankController::class)->except(['show']);
        Route::apiResource('receipts', AdminReceiptController::class);

        Route::apiResource('customers', AdminCustomerController::class)->only(['index', 'show']);
        Route::put('customers/{id}/flag', [AdminCustomerController::class, 'updateFlag']);
        Route::apiResource('orders', AdminOrderController::class)->only(['index', 'show']);
        Route::put('orders/{id}/status', [AdminOrderController::class, 'updateStatus']);
        Route::apiResource('transfers', TransferController::class)->only(['index', 'show', 'update']);
        Route::apiResource('storages', WarehouseController::class);
        Route::apiResource('shipments', ShipmentController::class);
    });
});

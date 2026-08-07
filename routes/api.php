<?php

use App\Http\Controllers\Api\AuthController;

use App\Http\Controllers\Api\Admin\AccuController as AdminAccuController;
use App\Http\Controllers\Api\Admin\BankController as AdminBankController;
use App\Http\Controllers\Api\Admin\CityAccuPriceController;
use App\Http\Controllers\Api\Admin\CityController as AdminCityController;
use App\Http\Controllers\Api\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Api\Admin\DashboardStatsController;
use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\Admin\ReceiptController as AdminReceiptController;
use App\Http\Controllers\Api\Admin\PickupPricingController;
use App\Http\Controllers\Api\Admin\ReportController;
use App\Http\Controllers\Api\Admin\SettingController;
use App\Http\Controllers\Api\Admin\ShipmentController;
use App\Http\Controllers\Api\Admin\TransferController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\WarehouseController;
use App\Http\Controllers\Api\Customer\AccuController as CustomerAccuController;
use App\Http\Controllers\Api\Customer\BankController as CustomerBankController;
use App\Http\Controllers\Api\Customer\CityController as CustomerCityController;
use App\Http\Controllers\Api\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Api\Customer\ReceiptController as CustomerReceiptController;
use App\Http\Controllers\Api\Customer\StorageController as CustomerStorageController;

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Api\Customer\OCRController as CustomerOCRController;
Route::post('/login', [AuthController::class, 'login']);

Route::prefix('customer')->group(function () {
    Route::get('cities', [CustomerCityController::class, 'index']);
    Route::get('cities/{cityId}/accus', [CustomerAccuController::class, 'getByCity']);
    Route::get('new-accus', [\App\Http\Controllers\Api\Customer\NewAccuController::class, 'index']);
    Route::get('storages', [CustomerStorageController::class, 'index']);
    Route::get('banks', [CustomerBankController::class, 'index']);
    Route::post('orders', [CustomerOrderController::class, 'store']);
    Route::get('orders/{id}', [CustomerOrderController::class, 'show']);
    Route::put('orders/{id}/note', [CustomerOrderController::class, 'updateNote']);
    Route::get('receipts/{orderId}', [CustomerReceiptController::class, 'show']);
    Route::post('orders/{id}/confirm-edit', [CustomerReceiptController::class, 'confirmEdit']);
    Route::post('ocr/extract-name', [CustomerOCRController::class, 'extractName']);
    Route::post('ocr/verify-proof', [CustomerOCRController::class, 'verifyProof']);
    Route::post('calculate-pickup-fee', [CustomerOrderController::class, 'calculatePickupFee']);
});

Route::prefix('public-admin')->group(function () {
    $verifyEasterEgg = function (\Illuminate\Http\Request $request) {
        $secret = $request->header('X-Easter-Egg-Pass') ?? $request->input('secret');
        if (!$secret) return false;

        $hashedSecret = hash('sha256', $secret);

        $hashPengguna = env('EASTER_EGG_HASH', 'b41eb90d70bb92b80236bb365ed3d12c3e224dd499bcc7194a789ee4f6ebcc10'); // aadu_imli
        $hashPengiriman = env('EASTER_EGG_PENGIRIMAN_HASH', '60cbb966fd55bb7abd3ade25b52c7e5cd1aefa05ded90298dbd7f31fda96b435'); // jojo_ganteng

        $path = $request->path();

        if ($path === 'api/public-admin/verify') {
            $referer = $request->headers->get('referer', '');
            if (str_contains($referer, '/pengiriman') || str_contains($referer, '/biaya-penjemputan')) {
                return $hashedSecret === $hashPengiriman;
            } else {
                return $hashedSecret === $hashPengguna;
            }
        }

        if (str_contains($path, 'pengiriman')) {
            return $hashedSecret === $hashPengiriman;
        }

        return $hashedSecret === $hashPengguna;
    };

    Route::post('verify', function (\Illuminate\Http\Request $request) use ($verifyEasterEgg) {
        if ($verifyEasterEgg($request)) {
            return response()->json(['message' => 'Akses rahasia dikonfirmasi!', 'valid' => true]);
        }
        return response()->json(['message' => 'Password rahasia salah!'], 401);
    });

    Route::get('users', function (\Illuminate\Http\Request $request) use ($verifyEasterEgg) {
        if ($verifyEasterEgg($request)) {
            return (new UserController)->index();
        }
        return response()->json(['message' => 'Akses ditolak'], 403);
    });

    Route::post('users', function (\Illuminate\Http\Request $request) use ($verifyEasterEgg) {
        if ($verifyEasterEgg($request)) {
            $req = app(\App\Http\Requests\Admin\StoreUserRequest::class);
            return (new UserController)->store($req);
        }
        return response()->json(['message' => 'Akses ditolak'], 403);
    });

    Route::delete('users/{id}', function (\Illuminate\Http\Request $request, int $id) use ($verifyEasterEgg) {
        if ($verifyEasterEgg($request)) {
            return (new UserController)->destroy($id);
        }
        return response()->json(['message' => 'Akses ditolak'], 403);
    });

    Route::get('pengiriman', function (\Illuminate\Http\Request $request) use ($verifyEasterEgg) {
        if ($verifyEasterEgg($request)) {
            return (new \App\Http\Controllers\Api\Admin\PickupPricingController)->index($request);
        }
        return response()->json(['message' => 'Akses ditolak'], 403);
    });

    Route::put('pengiriman', function (\Illuminate\Http\Request $request) use ($verifyEasterEgg) {
        if ($verifyEasterEgg($request)) {
            return (new \App\Http\Controllers\Api\Admin\PickupPricingController)->update($request);
        }
        return response()->json(['message' => 'Akses ditolak'], 403);
    });

    Route::get('pengiriman/history', function (\Illuminate\Http\Request $request) use ($verifyEasterEgg) {
        if ($verifyEasterEgg($request)) {
            return (new \App\Http\Controllers\Api\Admin\PickupPricingController)->history($request);
        }
        return response()->json(['message' => 'Akses ditolak'], 403);
    });
});


Route::get('admin/storages/{id}/stock', [WarehouseController::class, 'stockSummary']);
Route::put('admin/orders/{id}/items', [AdminOrderController::class, 'updateItems']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/admin/profile', [AuthController::class, 'updateProfile']);

    Route::prefix('admin')->group(function () {
        Route::get('settings', [SettingController::class, 'index']);
        Route::put('settings', [SettingController::class, 'update']);
        Route::get('price-histories', [SettingController::class, 'history']);
        
        Route::get('activities', [\App\Http\Controllers\Api\Admin\ActivityController::class, 'index']);
        Route::delete('activities/{id}', [\App\Http\Controllers\Api\Admin\ActivityController::class, 'destroy']);

        Route::get('dashboard-stats', [DashboardStatsController::class, 'index']);
        Route::get('reports', [ReportController::class, 'index']);
        Route::apiResource('users', UserController::class);

        Route::get('cities/trashed', [AdminCityController::class, 'trashed']);
        Route::post('cities/{id}/restore', [AdminCityController::class, 'restore']);
        Route::apiResource('cities', AdminCityController::class);

        Route::get('accus/trashed', [AdminAccuController::class, 'trashed']);
        Route::post('accus/{id}/restore', [AdminAccuController::class, 'restore']);
        Route::apiResource('accus', AdminAccuController::class);

        Route::apiResource('new-accus', \App\Http\Controllers\Api\Admin\NewAccuController::class);

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
        Route::put('orders/{id}/items', [AdminOrderController::class, 'updateItems']);
        Route::apiResource('transfers', TransferController::class)->only(['index', 'show', 'update']);

        Route::get('storages/trashed', [WarehouseController::class, 'trashed']);
        Route::get('storages/ready-to-pickup', [WarehouseController::class, 'readyToPickup']);
        Route::post('storages/{id}/pickup', [WarehouseController::class, 'pickup']);
        Route::post('storages/{id}/restore', [WarehouseController::class, 'restore']);
        Route::get('storages/{id}/stock', [WarehouseController::class, 'stockSummary']);
        Route::apiResource('storages', WarehouseController::class);
        Route::apiResource('shipments', ShipmentController::class);

        // Pickup Pricing Management renamed to pengiriman
        Route::get('pengiriman', [PickupPricingController::class, 'index']);
        Route::put('pengiriman', [PickupPricingController::class, 'update']);
        Route::get('pengiriman/history', [PickupPricingController::class, 'history']);
    });
});

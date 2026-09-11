<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckPickupSession;
use App\Http\Controllers\Api\Customer\SessionController;
use App\Models\Order;
use Illuminate\Http\Request;

// ── LANDING PAGE ──
Route::get('/', function () {
    return view('user.landing');
});
Route::get('/user', function () {
    return redirect('/');
});

// ── ENDPOINT SIMPAN SESSION DARI FRONTEND ──
Route::post('/save-checkout-session', [SessionController::class, 'storeCheckoutSession']);

// ── USER FLOW (PROTECTED BY SESSION MIDDLEWARE) ──
Route::middleware([CheckPickupSession::class])->group(function () {
    $identityHandler = function () {
        $paymentMethods = \App\Models\PaymentMethod::where('is_active', true)->get();
        return view('user.identity', compact('paymentMethods'));
    };

    Route::get('/identity', $identityHandler);

    Route::get('/trade-in', function () {
        return view('user.trade-in');
    });

    Route::get('/user/identitas', $identityHandler);
});

// ── RECEIPT ──
$receiptHandler = function (Request $request) {
    $orderUuid = $request->query('order_id');

    // 1. Jika parameter order_id tidak ada, langsung 404
    if (empty($orderUuid)) {
        abort(404);
    }

    // 2. Cek apakah UUID terdaftar di tabel orders
    $exists = Order::where('uuid', $orderUuid)->exists();

    // 3. Jika tidak ada di DB, langsung 404
    if (!$exists) {
        abort(404);
    }

    return view('user.receipt');
};

Route::get('/receipt', $receiptHandler);
Route::get('/user/receipt', $receiptHandler);

// ── ADMIN ROUTES ──
Route::get('/admin', function () {
    return redirect('admin/login');
});
Route::get('/admin/login', function () {
    return view('admin.login');
})->name('login');

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    });

    Route::get('/transaksi', function () {
        return view('admin.orders');
    });

    Route::get('/aktivitas', function () {
        return view('admin.activities');
    });

    Route::get('/harga', function () {
        return view('admin.prices');
    });

    Route::get('/gudang', function () {
        return view('admin.storages');
    });

    Route::get('/gudang/{id}', function ($id) {
        return view('admin.storage-detail', compact('id'));
    });

    Route::get('/pengguna', function () {
        return view('admin.users');
    });

    Route::get('/laporan', function () {
        return view('admin.reports');
    });

    Route::get('/audit-log-order', function () {
        return view('admin.audit-log-order');
    });

    Route::get('/pengiriman', function () {
        return view('admin.pickup-pricing');
    });
});
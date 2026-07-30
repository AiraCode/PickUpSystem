<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('user.landing');
});
Route::get('/user', function () {
    return redirect('/');
});

Route::get('/identity', function () {
    $paymentMethods = \App\Models\PaymentMethod::where('is_active', true)->get();
    return view('user.identity', compact('paymentMethods'));
});
Route::get('/trade-in', function () {
    return view('user.trade-in');
});
Route::get('/user/identitas', function () {
    $paymentMethods = \App\Models\PaymentMethod::where('is_active', true)->get();
    return view('user.identity', compact('paymentMethods'));
});

Route::get('/receipt', function () {
    return view('user.receipt');
});
Route::get('/user/receipt', function () {
    return view('user.receipt');
    return view('user.landing');
});
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

    Route::get('/harga', function () {
        return view('admin.prices');
    });

    Route::get('/gudang', function () {
        return view('admin.storages');
    });

    Route::get('/pengguna', function () {
        return view('admin.users');
    });

    Route::get('/laporan', function () {
        return view('admin.reports');
    });

    Route::get('/pengiriman', function () {
        return view('admin.pickup-pricing');
    });
});

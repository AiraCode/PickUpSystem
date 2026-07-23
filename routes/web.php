<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin/dashboard');
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
});

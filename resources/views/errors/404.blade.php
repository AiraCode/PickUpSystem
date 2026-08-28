@extends('user.layouts.app')

@section('content')
    <!-- Tambahkan style flexbox di sini -->
    <div id="top" style="display: flex; flex-direction: column; min-height: 100vh;">
        
        @include('user.partials.header', ['headerClass' => 'user-header--solid user-header--simple', 'hideNav' => true])

        <!-- Ganti min-height: 70vh dengan flex-grow: 1 -->
        <main class="user-flow-page" style="flex-grow: 1; display: flex; align-items: center; justify-content: center; padding: 60px 20px;">
            <div class="user-container" style="text-align: center; max-width: 500px;">
                <div style="font-size: 80px; font-weight: 800; color: #2563eb; line-height: 1; margin-bottom: 16px;">
                    404
                </div>
                
                <h1 style="font-size: 24px; font-weight: 700; color: #0f172a; margin-bottom: 12px;">
                    Halaman Tidak Ditemukan
                </h1>
                
                <p style="font-size: 14px; color: #64748b; margin-bottom: 28px; line-height: 1.6;">
                    Maaf, halaman atau transaksi yang Anda cari tidak ditemukan, telah dihapus, atau tautan yang Anda masukkan salah.
                </p>

                <a href="/" class="user-button user-button--primary" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px;">
                    <span>Kembali ke Beranda</span>
                    <span aria-hidden="true">→</span>
                </a>
            </div>
        </main>
    </div>
@endsection
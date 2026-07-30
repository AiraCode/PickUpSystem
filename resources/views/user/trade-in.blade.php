@extends('user.layouts.app')

@section('content')
    <div id="top">
        @include('user.partials.header', ['headerClass' => 'user-header--solid user-header--simple', 'hideNav' => true])

        <main class="user-flow-page">
            <section class="user-flow-hero">
                <div class="user-flow-hero__bg">
                    <div class="hero-slideshow">
                        <div class="hero-slide is-active" style="background-image: url('{{ asset('img/Aki_GS_Hijau-removebg-preview.png') }}');"></div>
                        <div class="hero-slide" style="background-image: url('{{ asset('img/Aki_GS_Putih-removebg-preview.png') }}');"></div>
                        <div class="hero-slide" style="background-image: url('{{ asset('img/Aki_Yuasa-removebg-preview.png') }}');"></div>
                    </div>
                </div>
                <div class="user-flow-hero__overlay"></div>

                <div class="user-container user-flow-hero__inner">
                    <div class="user-progress">
                        <div class="user-progress__step is-complete">
                            <span>01</span>
                            <small>Aki Reject</small>
                        </div>

                        <span class="user-progress__line is-complete"></span>

                        <div class="user-progress__step is-current">
                            <span>02</span>
                            <small>Pilih Aki Baru</small>
                        </div>

                        <span class="user-progress__line"></span>

                        <div class="user-progress__step">
                            <span>03</span>
                            <small>Identitas</small>
                        </div>

                        <span class="user-progress__line"></span>

                        <div class="user-progress__step">
                            <span>04</span>
                            <small>Receipt</small>
                        </div>
                    </div>

                    <span class="user-kicker">KATALOG AKI BARU</span>

                    <h1>
                        Pilih Aki Baru Anda untuk<br>
                        <em>Tukar Tambah.</em>
                    </h1>

                    <p>
                        Dapatkan harga aki baru terbaik dan potongan harga dari penjualan aki reject Anda.
                    </p>
                </div>
            </section>

            <section class="user-catalog user-section" id="daftar-aki-baru" style="padding-top: 40px;">
                <div class="user-container">
                    <div class="user-catalog-toolbar">
                        <label class="user-field user-field--search" for="new-accu-search-input" style="flex:1;">
                            <span>CARI JENIS AKI BARU</span>
                            <input type="text" id="new-accu-search-input" placeholder="Cari jenis aki (NX100, NS40, dll)..."
                                style="padding: 10px 14px; border: 1px solid #94a3b8; border-radius: 8px; font-size: 13px; width: 100%; height: 44px; box-sizing: border-box; outline-color: #2563eb;">
                        </label>
                    </div>

                    <div class="user-catalog-layout">
                        <div class="user-catalog-main">
                            <div class="user-catalog-grid" id="new-accus-grid">
                                <!-- JS Rendered -->
                                <div style="padding:40px; text-align:center; color:#64748b; font-size:14px;">Memuat data aki baru...</div>
                            </div>
                        </div>

                        <aside class="user-checkout-panel">
                            <div class="user-checkout-panel__header">
                                <h3>Ringkasan Tukar Tambah</h3>
                            </div>

                            <!-- Summary Aki Reject dari Landing Page -->
                            <div id="reject-accu-summary"></div>

                            <!-- Aki Baru Pilihan -->
                            <div class="user-cart">
                                <div id="new-accu-selected" style="padding: 20px 0; text-align: center; color: #64748b;">
                                    <span style="display:block; font-size:24px; margin-bottom:8px;">
                                        <svg viewBox="0 0 24 24" style="width:24px; height:24px; fill:none; stroke:currentColor; stroke-width:2; margin:auto;">
                                            <rect x="2" y="7" width="20" height="15" rx="2" ry="2"/>
                                            <polyline points="17 2 12 7 7 2"/>
                                        </svg>
                                    </span>
                                    <strong>Belum ada aki baru dipilih</strong>
                                    <p>Silakan pilih 1 aki dari katalog.</p>
                                </div>
                            </div>

                            <!-- Summary Kalkulasi Net Trade-In -->
                            <div id="trade-in-net-summary"></div>

                            <button type="button" id="btn-trade-in-continue"
                                class="user-button user-button--primary user-button--full"
                                style="margin-top: 20px; opacity:0.5; pointer-events:none;">
                                Lanjut Isi Identitas <span aria-hidden="true">→</span>
                            </button>
                            <p style="margin-top:12px; font-size:11px; color:#64748b; line-height:1.4;">
                                *Catatan: Anda akan diarahkan ke halaman pengisian identitas. Data aki reject yang sudah Anda pilih sebelumnya tetap tersimpan.
                            </p>
                        </aside>
                    </div>
                </div>
            </section>
        </main>

        <!-- Modal Konfirmasi Hapus Aki -->
        <div id="modal-delete-confirm" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:10000; align-items:center; justify-content:center; padding: 20px;">
            <div style="background:#fff; border-radius:16px; width:380px; max-width:100%; box-shadow: 0 10px 30px rgba(0,0,0,0.3); overflow:hidden; border: 1px solid #e2e8f0; text-align:center; padding: 24px;">
                <span style="font-size: 36px; display:block; margin-bottom: 8px;">🗑️</span>
                <h3 style="margin:0 0 6px; font-size:16px; font-weight:700; color:#0f172a;">Hapus Aki Pilihan</h3>
                <p id="delete-confirm-message" style="font-size:13px; color:#64748b; margin:0 0 20px; line-height:1.5;">Apakah Anda yakin ingin menghapus aki ini dari pilihan?</p>
                <div style="display:flex; gap:10px;">
                    <button type="button" id="btn-cancel-delete" class="user-button user-button--secondary" style="flex:1;">Batal</button>
                    <button type="button" id="btn-action-delete" class="user-button user-button--primary" style="flex:1; background:#ef4444; border-color:#ef4444; color:#fff;">Hapus</button>
                </div>
            </div>
        </div>

        @include('user.partials.footer')
    </div>
@endsection

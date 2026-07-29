@extends('user.layouts.app')

@section('content')
    <div id="top">
        @include('user.partials.header', ['headerClass' => 'user-header--hero'])

        <main>
            <section class="user-hero" style="min-height: 40vh; padding: 120px 0 60px;">
                <div class="user-hero__media" aria-label="Area gambar hero">
                    <div class="hero-slideshow">
                        <div class="hero-slide is-active" style="background-image: url('{{ asset('img/Aki_GS_Hijau-removebg-preview.png') }}');"></div>
                        <div class="hero-slide" style="background-image: url('{{ asset('img/Aki_GS_Putih-removebg-preview.png') }}');"></div>
                        <div class="hero-slide" style="background-image: url('{{ asset('img/Aki_Yuasa-removebg-preview.png') }}');"></div>
                    </div>
                    <span class="user-hero__overlay" aria-hidden="true"></span>
                </div>
                <div class="user-hero__content user-container">
                    <div class="user-hero__copy">
                        <h1>
                            Pilih Aki Baru Anda untuk<br>
                            <em>Tukar Tambah</em>
                        </h1>
                        <p>Dapatkan harga aki baru terbaik dan potongan harga dari penjualan aki reject Anda.</p>
                        <div class="user-hero__actions">
                            <a href="#daftar-aki-baru" class="user-button user-button--primary">Pilih Aki Baru <span
                                    aria-hidden="true">→</span></a>
                        </div>
                    </div>
                </div>
            </section>

            <section class="user-catalog user-section" id="daftar-aki-baru">
                <div class="user-container">
                    <div class="user-section-heading">
                        <span class="user-kicker">KATALOG AKI BARU</span>
                        <h2>Aki baru berkualitas<br><em>langsung dipasang.</em></h2>
                        <p style="max-width: none; font-size: 16px;">Pilih 1 (satu) aki baru yang ingin Anda beli.</p>
                    </div>

                    <div class="user-catalog-toolbar">
                        <label class="user-field user-field--search" for="new-accu-search-input" style="flex:1;">
                            <span>CARI JENIS AKI BARU</span>
                            <input type="text" id="new-accu-search-input" placeholder="Cari jenis aki (NX100, NS40, dll)...">
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
                                <h3>Aki Baru Pilihan Anda</h3>
                            </div>
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

                            <button type="button" id="btn-trade-in-continue"
                                class="user-button user-button--primary user-button--full"
                                style="margin-top: 20px; opacity:0.5; pointer-events:none;">
                                Lanjut Isi Identitas <span aria-hidden="true">→</span>
                            </button>
                            <p style="margin-top:12px; font-size:11px; color:#64748b; line-height:1.4;">
                                *Catatan: Anda akan diarahkan ke halaman pengisian identitas. Data aki reject yang sudah Anda pilih sebelumnya akan tetap tersimpan.
                            </p>
                        </aside>
                    </div>
                </div>
            </section>
        </main>

        @include('user.partials.footer')
    </div>
@endsection

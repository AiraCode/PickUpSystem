@extends('user.layouts.app')

@section('content')
    @include('user.partials.header', ['headerClass' => 'user-header--solid user-header--simple', 'hideNav' => true])

    <main class="user-flow-page user-receipt-page">
        <section class="user-flow-hero user-flow-hero--receipt">
            <div class="user-flow-hero__bg">
                <div class="hero-slideshow">
                    <div class="hero-slide is-active" style="background-image: url('{{ asset('img/Aki_GS_Hijau-removebg-preview.png') }}');"></div>
                    <div class="hero-slide" style="background-image: url('{{ asset('img/Aki_GS_Putih-removebg-preview.png') }}');"></div>
                    <div class="hero-slide" style="background-image: url('{{ asset('img/Aki_Yuasa-removebg-preview.png') }}');"></div>
                </div>
            </div>
            <div class="user-flow-hero__overlay"></div>

            <div class="user-container user-flow-hero__inner">
                <div class="user-progress" id="receipt-progress-bar">
                    <div class="user-progress__step is-complete"><span>01</span><small>Aki Reject</small></div>
                    <span class="user-progress__line is-complete"></span>
                    <div class="user-progress__step is-complete"><span>02</span><small>Identitas</small></div>
                    <span class="user-progress__line is-complete"></span>
                    <div class="user-progress__step is-current"><span>03</span><small>Receipt</small></div>
                </div>
                <span class="user-kicker">DOKUMEN PENJUALAN</span>
                <h1><span>Receipt pesanan</span><br><em>One Stop Solution.</em></h1>
                <p>Simpan receipt ini sebagai referensi transaksi. Informasi identitas sensitif tidak ditampilkan pada dokumen ini.</p>
            </div>
        </section>

        <section class="user-receipt-section">
            <div class="user-container">
                <div class="user-receipt-toolbar">
                    <div><span class="user-kicker">PREVIEW RECEIPT</span><p>Pilih status untuk melihat variasi dokumen yang sesuai.</p></div>
                    <div class="user-receipt-switch" role="group" aria-label="Status pembayaran">
                        <button type="button" class="is-active" data-receipt-status="unpaid">UNPAID</button>
                        <button type="button" data-receipt-status="paid">PAID</button>
                    </div>
                </div>

                <article class="user-receipt" data-receipt>
                    <div class="user-receipt__top">
                        <div class="user-receipt__brand">
                            <img src="{{ Vite::asset('resources/img/logo_admin2-removebg-preview.png') }}" alt="Modern Mulya Mandiri" style="height: 45px; width: auto; object-fit: contain;">
                            <div><strong>Modern Mulya Mandiri</strong><small>One Stop Solution</small></div>
                        </div>
                        <div class="user-receipt__meta">
                            <span>RECEIPT PENJUALAN</span>
                            <strong># —</strong>
                            <small><span>Tanggal transaksi</span>: —</small>
                        </div>
                    </div>

                    <div class="user-receipt__rule"></div>

                    <div class="user-receipt__heading">
                        <div><span class="user-kicker">ORDER RECEIPT</span><h2>Ringkasan penjualan aki</h2></div>
                        <span class="user-receipt__status user-receipt__status--unpaid" data-receipt-badge>UNPAID</span>
                    </div>

                    <div class="user-receipt__grid">
                        <section class="user-receipt__block">
                            <span class="user-receipt__label">INFORMASI PENJUAL</span>
                            <dl><div><dt>Nama</dt><dd>—</dd></div><div><dt>Nomor WhatsApp</dt><dd>—</dd></div><div><dt>Bank</dt><dd>—</dd></div><div><dt>Nomor rekening</dt><dd>—</dd></div><div><dt>Alamat</dt><dd>—</dd></div></dl>
                        </section>
                        <section class="user-receipt__block">
                            <span class="user-receipt__label">DETAIL PENYERAHAN</span>
                            <dl>
                                <div><dt>Metode penyerahan</dt><dd>—</dd></div>
                                <div><dt>Gudang / kurir</dt><dd>—</dd></div>
                                <div><dt>Biaya penjemputan</dt><dd>—</dd></div>
                                <div>
                                    <dt>Catatan</dt>
                                    <dd id="receipt-note-display">—</dd>
                                </div>
                            </dl>
                        </section>
                    </div>

                    <div class="user-receipt__table-wrap">
                        <table class="user-receipt__table">
                            <thead><tr><th>AKI</th><th>QTY</th><th>HARGA UNIT</th><th>SUBTOTAL</th></tr></thead>
                            <tbody><tr><td colspan="4"><div class="user-receipt__empty"><strong>Detail aki belum tersedia</strong><span>Item akan tampil setelah transaksi terhubung.</span></div></td></tr></tbody>
                        </table>
                    </div>

                    <div class="user-receipt__summary">
                        <div><span>Subtotal</span><strong>—</strong></div>
                        <div><span>Biaya penjemputan</span><strong>—</strong></div>
                        <div class="user-receipt__grand-total"><span>Total penjualan</span><strong>—</strong></div>
                    </div>

                    <section class="user-payment-proof" data-proof-section hidden>
                        <div class="user-payment-proof__head"><div><span class="user-receipt__label">BUKTI PEMBAYARAN</span><h3>Pembayaran telah diterima</h3></div><span class="user-receipt__status user-receipt__status--paid">PAID</span></div>
                        <div class="user-payment-proof__grid">
                            <dl><div><dt>Tanggal transfer</dt><dd>—</dd></div><div><dt>Nomor referensi</dt><dd>—</dd></div></dl>
                            <div class="user-payment-proof__image"><img src="{{ asset('images/not-found/payment/payment-proof.jpg') }}" alt="Bukti transfer belum tersedia (not found)"><span class="user-image-not-found">Bukti transfer · not found</span></div>
                        </div>
                    </section>

                    <div class="user-receipt__note"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8.5" /><path d="M12 10v5M12 7.5h.01" /></svg><p>KTP/SIM dan gambar identitas tidak ditampilkan pada receipt ini untuk menjaga privasi penjual.</p></div>

                    <div id="receipt-cancel-reason" style="display:none; background: #fee2e2; border: 1px solid #ef4444; color: #b91c1c; padding: 15px; border-radius: 8px; margin-top: 20px;">
                        <strong>Pesanan Dibatalkan</strong>
                        <p style="margin-top: 5px; font-size: 13px;" id="cancel-reason-text"></p>
                    </div>
                </article>

                <div class="user-receipt-actions">
                    <a href="/user" class="user-button user-button--secondary">Kembali ke halaman utama</a>
                    <button type="button" class="user-button user-button--primary" onclick="window.print()">Cetak receipt</button>
                </div>
            </div>
        </section>
    </main>

    @include('user.partials.footer')
@endsection



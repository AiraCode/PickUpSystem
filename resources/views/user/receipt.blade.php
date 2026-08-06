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
                <div id="receipt-edit-pending-banner" style="display:none; background:#fffbeb; border:1px solid #fde68a; border-radius:12px; padding:16px; margin-bottom:20px; box-shadow:0 4px 12px rgba(217,119,6,0.15);">
                    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
                        <div style="display:flex; align-items:center; gap:12px;">
                            <span style="font-size:28px;">⚠️</span>
                            <div>
                                <strong style="color:#b45309; font-size:14px; display:block;">Pemberitahuan Perubahan Pesanan oleh Admin</strong>
                                <span style="color:#92400e; font-size:12px; line-height:1.4; display:block;">Admin telah memperbarui rincian item aki pada pesanan Anda. Silakan tinjau dan konfirmasi perubahan ini.</span>
                            </div>
                        </div>
                        <button type="button" id="btn-trigger-confirm-modal" class="user-button user-button--primary" style="background:#d97706; border-color:#b45309; padding:8px 18px; font-size:12px; font-weight:700;">
                            ⚡ Tinjau & Konfirmasi Perubahan
                        </button>
                    </div>
                </div>

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

                    <section id="user-receipt-proofs-container" style="margin-top: 24px; border-top: 1px dashed #cbd5e1; padding-top: 20px;">
                        <!-- Proof Warehouse Section -->
                        <div id="proof-warehouse-box" style="display:none; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px; margin-bottom:16px;">
                            <div style="display:flex; align-items:center; justify-content:space-between; cursor:pointer;" onclick="window.toggleProofContent('warehouse-proof-body', 'warehouse-proof-arrow')">
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <span style="font-size:22px;">📦</span>
                                    <div>
                                        <strong style="font-size:14px; color:#1e293b; display:block;">Bukti Barang Sampai di Gudang</strong>
                                        <span style="font-size:12px; color:#64748b;">Foto serah terima fisik aki yang diunggah oleh pihak gudang/kurir</span>
                                    </div>
                                </div>
                                <button type="button" style="background:#e2e8f0; color:#334155; border:none; padding:6px 14px; border-radius:6px; font-size:12px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
                                    <span id="warehouse-proof-text">🔍 Buka Foto Bukti Barang</span>
                                    <span id="warehouse-proof-arrow" style="transition:transform 0.2s; font-size:10px;">▼</span>
                                </button>
                            </div>
                            <div id="warehouse-proof-body" style="display:none; margin-top:16px; border-top:1px solid #e2e8f0; padding-top:16px; text-align:center;">
                                <img id="warehouse-proof-img" src="" alt="Bukti barang sampai di gudang" style="max-width:100%; max-height:450px; border-radius:8px; border:1px solid #cbd5e1; object-fit:contain; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);">
                            </div>
                        </div>

                        <!-- Proof Payment Section -->
                        <div id="proof-payment-box" style="display:none; background:#ecfdf5; border:1px solid #a7f3d0; border-radius:12px; padding:16px; margin-bottom:16px;">
                            <div style="display:flex; align-items:center; justify-content:space-between; cursor:pointer;" onclick="window.toggleProofContent('payment-proof-body', 'payment-proof-arrow')">
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <span style="font-size:22px;">💳</span>
                                    <div>
                                        <strong style="font-size:14px; color:#065f46; display:block;">Bukti Transfer Pembayaran (PAID)</strong>
                                        <span style="font-size:12px; color:#047857;" id="payment-proof-meta-text">Bukti transfer asli pelunasan pembayaran transaksi</span>
                                    </div>
                                </div>
                                <button type="button" style="background:#d1fae5; color:#065f46; border:none; padding:6px 14px; border-radius:6px; font-size:12px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
                                    <span id="payment-proof-text">🔍 Buka Foto Bukti Transfer</span>
                                    <span id="payment-proof-arrow" style="transition:transform 0.2s; font-size:10px;">▼</span>
                                </button>
                            </div>
                            <div id="payment-proof-body" style="display:none; margin-top:16px; border-top:1px solid #a7f3d0; padding-top:16px; text-align:center;">
                                <div id="payment-proof-details" style="font-size:12px; color:#065f46; margin-bottom:12px; text-align:left; background:#ffffff; padding:10px; border-radius:6px; border:1px solid #a7f3d0;"></div>
                                <img id="payment-proof-img" src="" alt="Bukti transfer pembayaran" style="max-width:100%; max-height:450px; border-radius:8px; border:1px solid #6ee7b7; object-fit:contain; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);">
                            </div>
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
    <div id="modal-user-confirm-edit"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:10001; align-items:center; justify-content:center; padding:16px;">
        <div style="background:#fff; border-radius:14px; width:480px; max-width:100%; box-shadow:0 20px 25px -5px rgba(0,0,0,0.2); overflow:hidden; border:1px solid #e2e8f0; padding:24px;">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
                <span style="font-size:28px;">⚠️</span>
                <h3 style="margin:0; font-size:17px; font-weight:700; color:#1e293b;">Konfirmasi Perubahan Pesanan</h3>
            </div>
            <p style="font-size:13px; color:#475569; margin:0 0 16px; line-height:1.5;">
                Admin telah memperbarui rincian item aki pada pesanan Anda. Silakan setujui atau tolak rincian baru di bawah ini:
            </p>

            <div id="confirm-edit-items-summary" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; margin-bottom:20px; font-size:13px;">
                <div style="font-weight:700; color:#334155; margin-bottom:8px;">Item Aki Terbaru:</div>
                <div id="confirm-edit-items-list" style="display:flex; flex-direction:column; gap:6px;"></div>
                <div style="border-top:1px solid #cbd5e1; margin-top:10px; padding-top:8px; display:flex; justify-content:space-between; font-weight:700; color:#10b981;">
                    <span>Total Estimasi Pembayaran:</span>
                    <span id="confirm-edit-total-owed">Rp 0</span>
                </div>
            </div>

            <div style="display:flex; gap:10px;">
                <button type="button" id="btn-reject-edit" class="user-button user-button--secondary" style="flex:1; padding:10px; font-size:13px; border-color:#fca5a5; color:#dc2626; background:#fef2f2;">
                    ❌ Tolak Perubahan
                </button>
                <button type="button" id="btn-accept-edit" class="user-button user-button--primary" style="flex:1; padding:10px; font-size:13px; background:#10b981; color:#fff;">
                    ✅ Saya Setuju
                </button>
            </div>
        </div>
    </div>

    @include('user.partials.footer')
@endsection



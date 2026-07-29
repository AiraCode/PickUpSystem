@extends('user.layouts.app')

@section('content')
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

                <div class="user-progress" id="identity-progress-bar">
                    <div class="user-progress__step is-complete">
                        <span>01</span>
                        <small>Aki Reject</small>
                    </div>

                    <span class="user-progress__line is-complete"></span>

                    <div class="user-progress__step is-current">
                        <span>02</span>
                        <small>Identitas</small>
                    </div>

                    <span class="user-progress__line"></span>

                    <div class="user-progress__step">
                        <span>03</span>
                        <small>Receipt</small>
                    </div>
                </div>

                <span class="user-kicker">LANGKAH 02 DARI 03</span>

                <h1>
                    Lengkapi identitas<br>
                    <em>penjual.</em>
                </h1>

                <p>
                    Informasi ini diperlukan untuk proses verifikasi dan pembayaran.
                    Data pribadi Anda diproses secara terbatas sesuai kebutuhan transaksi.
                </p>

            </div>

        </section>

        <section class="user-form-section">
            <div class="user-container user-form-layout">
                <form id="identityForm" class="user-identity-form" data-identity-form>
                    <div class="user-form-card">
                        <div class="user-form-card__head">
                            <div class="user-form-card__icon user-form-card__icon--blue"><svg viewBox="0 0 24 24"
                                    aria-hidden="true">
                                    <circle cx="12" cy="8" r="3.5" />
                                    <path d="M5 20c.8-3.2 3.1-5 7-5s6.2 1.8 7 5" />
                                </svg></div>
                            <div><span class="user-kicker">INFORMASI PENJUAL</span>
                                <h2>Data diri</h2>
                            </div>
                        </div>
                        <div class="user-form-grid">
                            {{-- UI upload KTP/SIM --}}
                            <div class="user-upload-field user-upload-field--desktop-full" id="upload-ktp-trigger" style="cursor: pointer;">
                                <input type="file" id="ktp-file-input" name="identity_document" accept=".png,.jpg,.jpeg" style="display: none;">
                                <span class="user-upload-field__icon"><svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M5 4.5h14v15H5z" />
                                        <path d="M8 15.5 10.8 12l2 2 1.7-2.2 2.5 3.7M8.5 8.5h.01" />
                                    </svg></span>
                                <span><strong id="ktp-filename-label">Upload foto KTP atau SIM</strong><small>PNG/JPEG</small></span>
                                <span class="user-upload-field__action">Pilih foto</span>
                            </div>
                            <p id="ktp-size-hint" class="user-hint" style="display: none; color: #ef4444; margin-top: 4px; font-size: 13px;">*maksimal ukuran file 10 MB</p>

                            {{-- UI upload Aki + KTP 1 Frame --}}
                            <div class="user-upload-field user-upload-field--desktop-full" id="upload-accu-ktp-trigger" style="cursor: pointer; margin-top: 10px;">
                                <input type="file" id="accu-ktp-file-input" name="accu_ktp_document" accept=".png,.jpg,.jpeg" style="display: none;">
                                <span class="user-upload-field__icon"><svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="18" height="18" rx="2" />
                                        <circle cx="8.5" cy="8.5" r="1.5" />
                                        <polyline points="21 15 16 10 5 21" />
                                    </svg></span>
                                <span><strong id="accu-ktp-filename-label">Upload foto Aki & KTP dalam 1 Frame</strong><small>PNG/JPEG</small></span>
                                <span class="user-upload-field__action">Pilih foto</span>
                            </div>
                            <p id="accu-ktp-size-hint" class="user-hint" style="display: none; color: #ef4444; margin-top: 4px; font-size: 13px;">*maksimal ukuran file 10 MB</p>

                            {{-- UI OCR nama lengkap --}}
                            <div class="user-ocr-field user-floating-field--full" id="ocr-name-wrapper" style="display: none;">
                                <label class="user-floating-field user-floating-field--full">
                                    <input type="text" name="full_name" placeholder=" " readonly required
                                        style="background: #f1f5f9; cursor: not-allowed; color: #0f172a; font-weight: 600;">
                                    <span>Nama Lengkap Sesuai KTP / SIM</span>
                                </label>
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-top: 6px; padding: 0 4px;">
                                    <div id="ocr-status" style="display: none; font-size: 12px; flex: 1; padding-right: 8px;"></div>
                                    <a href="#" id="view-ktp-btn" style="display: none; font-size: 12px; font-weight: 600; color: #2563eb; text-decoration: none; white-space: nowrap;">Lihat foto</a>
                                </div>
                            </div>
                            <div class="user-floating-field--full" id="manual-name-wrapper" style="display: none; margin-top: 4px;">
                                <p style="font-size: 12px; color: #ef4444; margin-bottom: 8px; padding-left: 4px;">Apakah nama yang diinputkan belum sesuai? Inputkan nama yang benar pada kolom di bawah ini</p>
                                <label class="user-floating-field user-floating-field--full">
                                    <input type="text" name="manual_full_name" placeholder=" " style="text-transform: uppercase;">
                                    <span>Input Nama Manual</span>
                                </label>
                            </div>
                            <label class="user-floating-field"><select name="bank_type" required>
                                    <option value="" selected disabled></option>
                                    <option>BCA</option>
                                    <option>Mandiri</option>
                                    <option>BRI</option>
                                    <option>BNI</option>
                                    <option>Bank lainnya</option>
                                </select><span>Jenis bank</span></label>
                            <div>
                                <label class="user-floating-field" style="margin-bottom:0;">
                                    <input type="text" name="account_number" inputmode="numeric" placeholder=" " required disabled style="cursor: not-allowed; background: #f1f5f9;">
                                    <span>Nomor rekening</span>
                                </label>
                                <div id="account-hint" style="color: var(--user-red); font-size: 11px; margin-top: 6px; margin-left: 4px;">*pilih bank terlebih dahulu</div>
                            </div>
                            <label class="user-floating-field user-floating-field--full"><input type="text"
                                    name="account_holder" placeholder=" " style="text-transform: uppercase;" required><span>Nama pemilik
                                    rekening</span></label>
                            <div class="user-floating-field--full">
                                <label class="user-floating-field" style="margin-bottom:0;">
                                    <input type="tel" name="whatsapp" placeholder=" " required>
                                    <span>Nomor WhatsApp aktif</span>
                                </label>
                                <div id="wa-hint-1" style="color: var(--user-red); font-size: 11px; margin-top: 6px; margin-left: 4px;">*pengisian diawali dengan 0 (cth: 08123456789)</div>
                                <div id="wa-hint-2" style="color: var(--user-red); font-size: 11px; margin-top: 4px; margin-left: 4px;">*jumlah nomor berkisar 10-13 digit</div>
                            </div>
                            </div>
                            <div id="trade-in-transfer-wrapper" style="display: none; margin-top: 16px;">
                                <p style="font-size: 13px; color: #475569; margin-bottom: 8px; font-weight: 500;">
                                    <svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="#2563eb" stroke-width="2" style="width: 16px; height: 16px; display: inline-block; vertical-align: text-bottom; margin-right: 4px;">
                                        <circle cx="12" cy="12" r="10" />
                                        <line x1="12" y1="16" x2="12" y2="12" />
                                        <line x1="12" y1="8" x2="12.01" y2="8" />
                                    </svg>
                                    Harap memberi bukti transfer untuk membayar biaya tambahan tukar tambah aki.
                                </p>
                                <div class="user-upload-field user-upload-field--desktop-full" id="upload-transfer-trigger" style="cursor: pointer;">
                                    <input type="file" id="transfer-proof-input" accept=".png,.jpg,.jpeg" style="display: none;">
                                    <span class="user-upload-field__icon">
                                        <svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="2" y="5" width="20" height="14" rx="2" />
                                            <line x1="2" y1="10" x2="22" y2="10" />
                                        </svg>
                                    </span>
                                    <span><strong id="transfer-filename-label">Upload Bukti Transfer Kekurangan Pembayaran</strong><small>PNG/JPEG</small></span>
                                    <span class="user-upload-field__action">Pilih foto</span>
                                </div>
                                <div id="transfer-ocr-status" style="margin-top: 8px; font-size: 12px; display: none;"></div>
                            </div>
                        </div>

                    <div class="user-form-card user-form-card--privacy">
                        <span class="user-form-card__icon user-form-card__icon--red"><svg viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path d="M6 10V8a6 6 0 0 1 12 0v2M5 10h14v10H5z" />
                                <path d="M12 14v2" />
                            </svg></span>
                        <div><strong>Informasi Anda terlindungi</strong>
                            <p>Foto identitas hanya digunakan untuk verifikasi internal dan tidak akan ditampilkan pada
                                receipt publik.</p>
                        </div>
                    </div>
                </form>

                <aside class="user-flow-summary">
                    <span class="user-kicker">RINGKASAN PESANAN</span>
                    <h2>Data penjualan Anda</h2>
                    <div class="user-flow-summary__item"><span>Item aki</span><strong>Menunggu data pilihan</strong></div>
                    <div class="user-flow-summary__item"><span>Metode penyerahan</span><strong>Menunggu pilihan</strong>
                    </div>
                    <div class="user-flow-summary__item"><span>Alamat</span><strong>Menunggu data alamat</strong></div>
                    <div class="user-flow-summary__total"><span>Total harga</span><strong>—</strong></div>
                    <div class="user-flow-summary__note"><svg viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="8.5" />
                            <path d="M12 10v5M12 7.5h.01" />
                        </svg><span>Pastikan data rekening dan WhatsApp aktif sebelum melanjutkan.</span></div>
                </aside>
                <div class="user-form-actions">
                    <a href="/user" class="user-button user-button--secondary">← Kembali</a>
                    <button type="button" id="btn-identity-next" class="user-button user-button--primary">
                        Konfirmasi Data
                        <span aria-hidden="true">→</span>
                    </button>
                </div>
            </div>
        </section>
    </main>

    <div class="user-modal" data-identity-modal hidden>
        <div class="user-modal__backdrop" data-modal-close></div>
        <div class="user-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="identity-modal-title" style="max-width: 800px; width: 95vw;">
            <button type="button" class="user-modal__close" data-modal-close aria-label="Tutup konfirmasi">×</button>
            <span class="user-modal__icon"><svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M5 12.5 9.5 17 19 7.5" />
                </svg></span>
            <span class="user-kicker">KONFIRMASI DATA</span>
            <h2 id="identity-modal-title">Apakah semua data<br>yang Anda masukkan sudah benar?</h2>
            <style>
                .modal-split-layout { display: grid; grid-template-columns: 1fr; gap: 16px; margin: 16px 0; text-align: left; }
                @media (min-width: 768px) {
                    .modal-split-layout { grid-template-columns: 1fr 1fr; }
                }
                .modal-cart-table { width: 100%; border-collapse: collapse; }
                .modal-cart-table th, .modal-cart-table td { padding: 8px 4px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
                .modal-cart-table th { color: #64748b; font-weight: 600; font-size: 10px; }
            </style>
            <div class="modal-split-layout">
                <div id="modal-data-summary" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; font-size: 13px; color: #334155; line-height: 1.5; height: fit-content;">
                    <div style="margin-bottom: 8px;"><strong style="color:#0f172a; display:inline-block; width:110px;">Nama Lengkap</strong>: <span id="summary-nama"></span></div>
                    <div style="margin-bottom: 8px;"><strong style="color:#0f172a; display:inline-block; width:110px;">WhatsApp</strong>: <span id="summary-wa"></span></div>
                    <div style="margin-bottom: 8px;"><strong style="color:#0f172a; display:inline-block; width:110px;">Rekening Bank</strong>: <span id="summary-bank"></span></div>
                    <div style="margin-bottom: 8px;"><strong style="color:#0f172a; display:inline-block; width:110px;">Alamat</strong>: <span id="summary-alamat"></span></div>
                    <div><strong style="color:#0f172a; display:inline-block; width:110px;">Catatan</strong>: <span id="summary-catatan"></span></div>
                </div>
                
                <div id="modal-order-summary" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; font-size: 13px; color: #334155; height: fit-content;">
                    <strong style="color:#0f172a; display:block; margin-bottom: 12px; font-size: 15px;">Rincian Harga</strong>
                    <div style="max-height: 200px; overflow-y: auto;">
                        <table class="modal-cart-table">
                            <thead style="position: sticky; top: 0; background: #f8fafc; z-index: 1;">
                                <tr>
                                    <th style="text-align: left;">AKI</th>
                                    <th style="text-align: center;">QTY</th>
                                    <th style="text-align: right;">HARGA UNIT</th>
                                    <th style="text-align: right;">SUBTOTAL</th>
                                </tr>
                            </thead>
                            <tbody id="modal-cart-items">
                            </tbody>
                        </table>
                    </div>
                    <div style="margin-top: 12px; padding-top: 12px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                            <span style="color: #64748b;">Subtotal</span>
                            <span id="modal-subtotal" style="color: #0f172a;">Rp 0</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                            <span style="color: #64748b;">Biaya penjemputan</span>
                            <span id="modal-fee" style="color: #0f172a;">Rp 0</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-top: 8px; padding-top: 8px; border-top: 1px solid #e2e8f0;">
                            <strong id="modal-total-label" style="color: #0f172a;">Total penjualan</strong>
                            <strong id="modal-total" style="color: var(--user-blue); font-size: 15px;">Rp 0</strong>
                        </div>
                    </div>
                </div>
            </div>
            <p style="margin: 0 auto; margin-bottom: 16px;">Pastikan nama, rekening, dan nomor WhatsApp sudah sesuai. Data ini akan digunakan untuk proses verifikasi dan
                pembayaran.</p>
            <div class="user-modal__actions">
                <button type="button" class="user-button user-button--secondary" data-modal-close>Kembali</button>
                <button type="button" id="btn-modal-confirm-submit" class="user-button user-button--primary">Konfirmasi <span
                        aria-hidden="true">→</span></button>
            </div>
        </div>
    </div>

    @include('user.partials.footer')

    <!-- Modal Lihat Foto KTP -->
    <div id="ktp-overlay" style="display: none; position: fixed; inset: 0; z-index: 10000; background: rgba(17, 19, 24, 0.85); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 20px;">
        <div style="position: relative; max-width: 90vw; max-height: 90vh; display: flex; flex-direction: column; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.5);">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; background: #111318; color: #fff;">
                <h3 style="margin: 0; font-size: 14px; font-weight: 600;">Foto KTP/SIM</h3>
                <button type="button" id="close-ktp-overlay" style="background: transparent; border: none; color: #a0a3aa; cursor: pointer; display: flex; align-items: center; padding: 4px; transition: color 0.2s;">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div style="padding: 16px; display: flex; justify-content: center; align-items: center; background: #f8fafc; overflow: auto;">
                <img id="ktp-overlay-img" src="" style="max-width: 100%; max-height: 70vh; border-radius: 8px; object-fit: contain;">
            </div>
        </div>
    </div>

<div id="modal-upload-choice" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:10000; align-items:center; justify-content:center; padding: 20px;">
    <div style="background:#fff; border-radius:16px; width:360px; max-width:100%; box-shadow: 0 10px 30px rgba(0,0,0,0.3); overflow:hidden; text-align:center; padding: 24px;">
        <span style="font-size: 36px; display:block; margin-bottom: 8px;">📷</span>
        <h3 style="margin:0 0 6px; font-size:17px; font-weight:700; color:#0f172a;">Metode Pengambilan Foto</h3>
        <p style="font-size:13px; color:#64748b; margin:0 0 20px;">Pilih cara pengunggahan foto dokumen yang Anda inginkan:</p>
        <div style="display:flex; flex-direction:column; gap:10px;">
            <button type="button" id="btn-choice-camera" class="user-button user-button--primary" style="background:#2563eb; color:#fff; width:100%; padding:10px 16px; font-weight:600; display:flex; align-items:center; justify-content:center; gap:8px;">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                Ambil dari Kamera (Live Frame)
            </button>
            <button type="button" id="btn-choice-gallery" class="user-button user-button--secondary" style="width:100%; padding:10px 16px; font-weight:600; display:flex; align-items:center; justify-content:center; gap:8px;">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                Pilih dari Galeri / File
            </button>
            <button type="button" id="btn-choice-cancel" style="background:none; border: 1px solid #ef4444; color:#ef4444; font-size:13px; font-weight:600; cursor:pointer; margin-top:4px; border-radius: 8px; padding: 10px 16px;">Batal</button>
        </div>
    </div>
</div>

<!-- Modal Live Camera dengan Arahan Bingkai -->
<div id="modal-live-camera" style="display:none; position:fixed; inset:0; background:#000; z-index:10001; flex-direction:column; align-items:center; justify-content:space-between; padding: 20px;">
    <div style="width:100%; display:flex; justify-content:space-between; align-items:center; color:#fff; z-index:10;">
        <h3 id="camera-modal-title" style="margin:0; font-size:15px; font-weight:600;">Ambil Foto Dokumen</h3>
        <button type="button" id="btn-close-camera" style="background:none; border:none; color:#fff; font-size:24px; cursor:pointer; padding:4px;">✕</button>
    </div>

    <div style="position:relative; width:100%; max-width:480px; flex:1; display:flex; align-items:center; justify-content:center; overflow:hidden; margin:16px 0;">
        <video id="camera-video" autoplay playsinline style="width:100%; height:100%; object-fit:cover; border-radius:12px;"></video>
        
        <!-- Frame Overlay (Bingkai Arahan) -->
        <div style="position:absolute; inset:20px; border:3px dashed #3b82f6; border-radius:16px; box-shadow:0 0 0 9999px rgba(0,0,0,0.6); pointer-events:none; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center;">
            <div style="background:rgba(37,99,235,0.85); color:#fff; padding:6px 14px; border-radius:20px; font-size:12px; font-weight:600; margin-bottom:12px;">
                📍 Posisikan objek tepat di dalam bingkai ini
            </div>
            <div style="width:50px; height:50px; border:2px solid rgba(255,255,255,0.5); border-radius:50%; display:flex; align-items:center; justify-content:center; opacity:0.6;">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="#fff" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
            </div>
        </div>
    </div>

    <div style="width:100%; max-width:480px; display:flex; justify-content:center; gap:20px; align-items:center; z-index:10; padding-bottom:10px;">
        <button type="button" id="btn-capture-photo" style="width:64px; height:64px; border-radius:50%; background:#fff; border:4px solid #2563eb; cursor:pointer; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 15px rgba(37,99,235,0.4);" title="Ambil Foto">
            <div style="width:46px; height:46px; border-radius:50%; background:#2563eb;"></div>
        </button>
    </div>
</div>
<canvas id="camera-canvas" style="display:none;"></canvas>

<div id="modal-user-alert" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:10000; align-items:center; justify-content:center; padding: 20px;">
    <div style="background:#fff; border-radius:12px; width:360px; max-width:100%; box-shadow: 0 10px 25px rgba(0,0,0,0.2); overflow:hidden; border: 1px solid #e2e8f0; text-align:center; padding: 24px;">
        <span style="font-size: 40px; display:block; margin-bottom: 12px;">⚠️</span>
        <h3 style="margin:0 0 8px; font-size:16px; font-weight:700; color:#1e293b;">Pemberitahuan</h3>
        <p id="user-alert-message" style="font-size:13px; color:#475569; margin:0 0 18px; line-height:1.5;"></p>
        <button type="button" class="user-button user-button--primary" style="background:#2563eb; color:#fff; width:100%; padding: 8px 16px;" onclick="document.getElementById('modal-user-alert').style.display='none'">Tutup</button>
    </div>
</div>
@endsection


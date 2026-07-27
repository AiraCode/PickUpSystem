@extends('user.layouts.app')

@section('content')
    @include('user.partials.header', ['headerClass' => 'user-header--solid user-header--simple', 'hideNav' => true])

    <main class="user-flow-page">
        <section class="user-flow-hero">

            <div class="user-flow-hero__bg"></div>
            <div class="user-flow-hero__overlay"></div>

            <div class="user-container user-flow-hero__inner">

                <div class="user-progress">
                    <div class="user-progress__step is-complete">
                        <span>01</span>
                        <small>Pilih aki</small>
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
                            <label class="user-upload-field user-upload-field--desktop-full">
                                <input type="file" name="identity_document" accept=".png,.jpg,.jpeg">
                                <span class="user-upload-field__icon"><svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M5 4.5h14v15H5z" />
                                        <path d="M8 15.5 10.8 12l2 2 1.7-2.2 2.5 3.7M8.5 8.5h.01" />
                                    </svg></span>
                                <span><strong>Upload foto KTP atau SIM</strong><small>PNG/JPEG</small></span>
                                <span class="user-upload-field__action">Pilih file</span>
                            </label>

                            {{-- UI OCR nama lengkap --}}
                            <div class="user-ocr-field user-floating-field--full" id="ocr-name-wrapper" style="display: none;">
                                <label class="user-floating-field user-floating-field--full">
                                    <input type="text" name="full_name" placeholder=" " readonly required
                                        style="background: #f1f5f9; cursor: not-allowed; color: #0f172a; font-weight: 600;">
                                    <span>Nama Lengkap Sesuai KTP / SIM</span>
                                </label>
                                <div id="ocr-status" style="display: none; font-size: 12px; margin-top: 6px; padding: 0 4px;"></div>
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
                    <div class="user-flow-summary__total"><span>Total estimasi</span><strong>—</strong></div>
                    <div class="user-flow-summary__note"><svg viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="8.5" />
                            <path d="M12 10v5M12 7.5h.01" />
                        </svg><span>Pastikan data rekening dan WhatsApp aktif sebelum melanjutkan.</span></div>
                </aside>
                <div class="user-form-actions">
                    <a href="/user" class="user-button user-button--secondary">← Kembali</a>
                    <button type="submit" form="identityForm" class="user-button user-button--primary">
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
                                    <th style="text-align: left;">AKI / BRAND</th>
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
                            <strong style="color: #0f172a;">Total penjualan</strong>
                            <strong id="modal-total" style="color: var(--user-blue); font-size: 15px;">Rp 0</strong>
                        </div>
                    </div>
                </div>
            </div>
            <p style="margin: 0 auto; margin-bottom: 16px;">Pastikan nama, rekening, dan nomor WhatsApp sudah sesuai. Data ini akan digunakan untuk proses verifikasi dan
                pembayaran.</p>
            <div class="user-modal__actions">
                <button type="button" class="user-button user-button--secondary" data-modal-close>Belum</button>
                <a href="/user/receipt" class="user-button user-button--primary">Sudah Benar <span
                        aria-hidden="true">→</span></a>
            </div>
        </div>
    </div>

    @include('user.partials.footer')
<div id="modal-user-alert" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:10000; align-items:center; justify-content:center; padding: 20px;">
    <div style="background:#fff; border-radius:12px; width:360px; max-width:100%; box-shadow: 0 10px 25px rgba(0,0,0,0.2); overflow:hidden; border: 1px solid #e2e8f0; text-align:center; padding: 24px;">
        <span style="font-size: 40px; display:block; margin-bottom: 12px;">⚠️</span>
        <h3 style="margin:0 0 8px; font-size:16px; font-weight:700; color:#1e293b;">Pemberitahuan</h3>
        <p id="user-alert-message" style="font-size:13px; color:#475569; margin:0 0 18px; line-height:1.5;"></p>
        <button type="button" class="user-button user-button--primary" style="background:#2563eb; color:#fff; width:100%; padding: 8px 16px;" onclick="document.getElementById('modal-user-alert').style.display='none'">Tutup</button>
    </div>
</div>
@endsection


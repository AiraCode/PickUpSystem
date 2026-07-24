@extends('user.layouts.app')

@section('content')
    @include('user.partials.header', ['headerClass' => 'user-header--solid'])

    <main class="user-flow-page">
        <section class="user-flow-hero">
            <div class="user-container user-flow-hero__inner">
                <a href="/user" class="user-back-link"><span aria-hidden="true">←</span> Kembali ke daftar harga</a>
                <div class="user-progress">
                    <div class="user-progress__step is-complete"><span>01</span><small>Pilih aki</small></div>
                    <span class="user-progress__line is-complete"></span>
                    <div class="user-progress__step is-current"><span>02</span><small>Identitas</small></div>
                    <span class="user-progress__line"></span>
                    <div class="user-progress__step"><span>03</span><small>Receipt</small></div>
                </div>
                <span class="user-kicker">LANGKAH 02 DARI 03</span>
                <h1>Lengkapi identitas<br><em>penjual.</em></h1>
                <p>Informasi ini diperlukan untuk proses verifikasi dan pembayaran. Data pribadi Anda diproses secara
                    terbatas sesuai kebutuhan transaksi.</p>
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
                            <label class="user-floating-field user-floating-field--full"><input type="text"
                                    name="full_name" placeholder=" " required><span>Nama lengkap</span></label>
                            <label class="user-upload-field user-upload-field--desktop-full">
                                <input type="file" name="identity_document" accept=".png,.jpg,.jpeg">
                                <span class="user-upload-field__icon"><svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M5 4.5h14v15H5z" />
                                        <path d="M8 15.5 10.8 12l2 2 1.7-2.2 2.5 3.7M8.5 8.5h.01" />
                                    </svg></span>
                                <span><strong>Upload foto KTP atau SIM</strong><small>PNG/JPEG · maksimal sesuai kebijakan
                                        sistem</small></span>
                                <span class="user-upload-field__action">Pilih file</span>
                            </label>
                            <label class="user-floating-field"><select name="bank_type" required>
                                    <option value="" selected disabled></option>
                                    <option>BCA</option>
                                    <option>Mandiri</option>
                                    <option>BRI</option>
                                    <option>BNI</option>
                                    <option>Bank lainnya</option>
                                </select><span>Jenis bank</span></label>
                            <label class="user-floating-field"><input type="text" name="account_number"
                                    inputmode="numeric" placeholder=" " required><span>Nomor rekening</span></label>
                            <label class="user-floating-field user-floating-field--full"><input type="text"
                                    name="account_holder" placeholder=" " required><span>Nama pemilik
                                    rekening</span></label>
                            <label class="user-floating-field user-floating-field--full"><input type="tel"
                                    name="whatsapp" placeholder=" " required><span>Nomor WhatsApp aktif</span></label>
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
                    <div class="user-flow-summary__total"><span>Total estimasi</span><strong>—</strong><small>Nilai akan
                            tersedia setelah data harga terhubung.</small></div>
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
        <div class="user-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="identity-modal-title">
            <button type="button" class="user-modal__close" data-modal-close aria-label="Tutup konfirmasi">×</button>
            <span class="user-modal__icon"><svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M5 12.5 9.5 17 19 7.5" />
                </svg></span>
            <span class="user-kicker">KONFIRMASI DATA</span>
            <h2 id="identity-modal-title">Apakah semua data<br>yang Anda masukkan sudah benar?</h2>
            <p>Pastikan nama, rekening, dan nomor WhatsApp sudah sesuai. Data ini akan digunakan untuk proses verifikasi dan
                pembayaran.</p>
            <div class="user-modal__actions">
                <button type="button" class="user-button user-button--secondary" data-modal-close>Belum</button>
                <a href="/user/receipt" class="user-button user-button--primary">Sudah Benar <span
                        aria-hidden="true">→</span></a>
            </div>
        </div>
    </div>
@endsection

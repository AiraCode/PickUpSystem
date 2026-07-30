@extends('user.layouts.app')

@section('content')
    <div id="top">
        @include('user.partials.header', ['headerClass' => 'user-header--hero'])

        <main>
            <section class="user-hero">
                <div class="user-hero__media" aria-label="Area gambar hero">
                    <div class="hero-slideshow">
                        <div class="hero-slide is-active"
                            style="background-image: url('{{ asset('img/Aki_GS_Hijau-removebg-preview.png') }}');"></div>
                        <div class="hero-slide"
                            style="background-image: url('{{ asset('img/Aki_GS_Putih-removebg-preview.png') }}');"></div>
                        <div class="hero-slide"
                            style="background-image: url('{{ asset('img/Aki_Yuasa-removebg-preview.png') }}');"></div>
                    </div>
                    <span class="user-hero__overlay" aria-hidden="true"></span>
                </div>
                <div class="user-hero__content user-container">
                    <div class="user-hero__copy">
                        <h1>
                            Jual Aki Reject Anda dengan<br>
                            <em>Harga Terbaik</em>
                        </h1>
                        <p>Modern Mulya Mandiri membeli aki mobil dan aki motor reject dengan proses cepat, transparan, dan
                            pembayaran langsung ke rekening Anda.</p>
                        <div class="user-hero__actions">
                            <a href="#daftar-harga" class="user-button user-button--primary">Jual Sekarang <span
                                    aria-hidden="true">→</span></a>
                        </div>
                    </div>
                </div>
            </section>

            <section class="user-process user-section" id="cara-menjual">
                <div class="user-container">
                    <div class="user-section-heading user-section-heading--split">
                        <div>
                            <span class="user-kicker">CARA MENJUAL</span>
                            <h2>Serahkan aki Anda<br><em>dalam tiga langkah.</em></h2>
                        </div>
                        <p>Semua proses dibuat sederhana agar Anda dapat mengetahui estimasi nilai aki dan metode penyerahan
                            dengan jelas.</p>
                    </div>
                    <div class="user-process-grid">
                        <article class="user-process-card">
                            <span class="user-process-card__number">01</span>
                            <div class="user-process-card__icon user-process-card__icon--blue"><svg viewBox="0 0 24 24"
                                    aria-hidden="true">
                                    <path d="M4.5 6.5h15v12h-15z" />
                                    <path d="M8 10h8M8 14h5" />
                                </svg></div>
                            <h3>Pilih aki dan wilayah</h3>
                            <p>Pilih jenis aki dan kota untuk melihat informasi harga yang tersedia pada sistem.</p>
                        </article>
                        <article class="user-process-card">
                            <span class="user-process-card__number">02</span>
                            <div class="user-process-card__icon user-process-card__icon--red"><svg viewBox="0 0 24 24"
                                    aria-hidden="true">
                                    <circle cx="12" cy="8" r="3.5" />
                                    <path d="M5 20c.8-3.2 3.1-5 7-5s6.2 1.8 7 5" />
                                </svg></div>
                            <h3>Lengkapi identitas</h3>
                            <p>Isi informasi penjual dan rekening yang diperlukan untuk verifikasi serta pembayaran.</p>
                        </article>
                        <article class="user-process-card">
                            <span class="user-process-card__number">03</span>
                            <div class="user-process-card__icon user-process-card__icon--blue"><svg viewBox="0 0 24 24"
                                    aria-hidden="true">
                                    <path d="M4.5 7.5h15v11h-15z" />
                                    <path d="M8 7.5V5.8h8v1.7M8 12h8M8 15.5h4" />
                                </svg></div>
                            <h3>Serahkan aki dan terima pembayaran</h3>
                            <p>Pilih penyerahan ke gudang atau penjemputan kurir sesuai kebutuhan Anda.</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="user-catalog user-section" id="daftar-harga">
                <div class="user-container">
                    <div class="user-section-heading">
                        <span class="user-kicker">KATALOG AKI</span>
                        <h2>Harga jual aki terbaik<br><em>se-Indonesia.</em></h2>
                        <p style="max-width: none; font-size: 16px;">Pilih kota untuk menampilkan data yang terhubung pada
                            sistem.</p>
                    </div>

                    <div class="user-catalog-toolbar">
                        <label class="user-field user-field--city" for="user-city">
                            <span>PILIH KOTA PENYERAHAN</span>
                            <select id="user-city" data-city-select>
                                <option value="" disabled selected>-- Pilih Kota Penyerahan --</option>
                                <option value="Surabaya">Surabaya</option>
                                <option value="Jakarta">Jakarta</option>
                                <option value="Bandung">Bandung</option>
                                <option value="Semarang">Semarang</option>
                                <option value="Yogyakarta">Yogyakarta</option>
                                <option value="Makassar">Makassar</option>
                                <option value="Palu">Palu</option>
                                <option value="Balikpapan">Balikpapan</option>
                            </select>
                        </label>
                        <label class="user-field user-field--search" for="accu-search-input">
                            <span>CARI JENIS AKI</span>
                            <input type="text" id="accu-search-input" placeholder="Cari jenis aki (NX100, NS40, dll)..."
                                style="padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; width: 100%; height: 44px; box-sizing: border-box;">
                        </label>
                        <div class="user-catalog-notice" id="user-city-warning" data-city-warning>
                            <strong>*Daftar harga aki berdasarkan wilayah yang dipilih.</strong>
                        </div>
                    </div>

                    <div class="user-catalog-layout">
                        <div class="user-catalog-main">
                            <div class="user-disabled-box" id="user-battery-disabled-placeholder"
                                data-battery-disabled-placeholder>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                <strong>*Silakan memilih kota terlebih dahulu</strong>
                                <p>Daftar aki dan informasi harga jual akan muncul secara otomatis setelah Anda menentukan
                                    lokasi penyerahan.</p>
                            </div>

                            <div class="user-battery-container" id="user-battery-container" style="display: none;"
                                data-battery-container>
                                <div class="user-store-header">
                                    <div class="user-store-header__title">
                                        <span class="user-store-badge">📍 GUDANG WILAYAH</span>
                                        <strong id="user-store-city-name" data-store-city>SURABAYA</strong>
                                    </div>
                                </div>

                                <div class="user-battery-list" id="user-battery-list">
                                </div>
                            </div>
                        </div>

                        <aside class="user-checkout-card" aria-label="Ringkasan penjualan" id="user-checkout-section">
                            <div class="user-checkout-card__head">
                                <div>
                                    <span class="user-kicker">RINGKASAN</span>
                                    <h2>Penjualan Anda</h2>
                                </div>
                                <span class="user-cart-count" data-cart-count>0</span>
                            </div>

                            <div class="user-cart-items" data-cart-items>
                                <div class="user-cart-empty" data-cart-empty>
                                    <span class="user-cart-empty__icon"><svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M4.5 5.5h15l-1.2 8.5H6.2z" />
                                            <path d="M7 5.5 8 3h8l1 2.5M9 18.5h.01M16 18.5h.01" />
                                        </svg></span>
                                    <strong>Belum ada aki dipilih</strong>
                                    <p>Tambahkan aki dari daftar untuk melihat rincian.</p>
                                </div>
                            </div>

                            {{-- <div class="user-delivery" style="margin-bottom: 20px;">
                                <span class="user-checkout-label">PILIHAN TRANSAKSI</span>
                                <label class="user-radio-card">
                                    <input type="radio" name="order_type_selection" value="sell" checked>
                                    <span class="user-radio-card__indicator"></span>
                                    <span><strong>Jual Aki Reject Saja</strong><small>Penjualan aki reject ke Modern Mulya
                                            Mandiri.</small></span>
                                </label>
                                <label class="user-radio-card">
                                    <input type="radio" name="order_type_selection" value="trade_in">
                                    <span class="user-radio-card__indicator"></span>
                                    <span><strong>Trade In (Tukar Tambah)</strong><small>Tukar tambah aki reject dengan aki
                                            baru.</small></span>
                                </label>
                            </div> --}}

                            <div class="user-delivery" data-delivery-method>
                                <span class="user-checkout-label">METODE PENYERAHAN AKI</span>
                                <label class="user-radio-card is-selected">
                                    <input type="radio" name="delivery_method" value="warehouse" checked data-pickup-method>
                                    <span class="user-radio-card__indicator"></span>
                                    <span><strong>Antar ke gudang</strong><small>Rekomendasi gudang terdekat ·
                                            biaya layanan gratis.</small></span>
                                </label>
                                <label class="user-radio-card">
                                    <input type="radio" name="delivery_method" value="courier" data-pickup-method>
                                    <span class="user-radio-card__indicator"></span>
                                    <span class="user-radio-card__content"
                                        style="display: flex; flex-direction: column; width: 100%;">
                                        <strong>Dijemput kurir</strong>
                                        <small>Biaya penjemputan akan dihitung berdasarkan jarak.</small>
                                        <style>
                                            .user-radio-card input:not(:checked)~span #pickup-fee-warning {
                                                display: none !important;
                                            }

                                            .user-radio-card input:checked~span #pickup-fee-warning {
                                                display: block !important;
                                            }
                                        </style>
                                        <small id="pickup-fee-warning" class="user-pickup-warning"
                                            style="color: #dc2626; font-size: 12px; font-weight: 600; padding: 6px 12px; margin-top: 8px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; line-height: 1.4;">
                                            ⚠️ Biaya penjemputan ditanggung oleh penjual dan akan mengurangi total
                                            pembayaran.
                                        </small>
                                    </span>
                                </label>
                            </div>

                            <div class="user-checkout-totals">
                                <div><span>Subtotal</span><strong data-cart-subtotal>—</strong></div>
                                <div><span>Biaya penjemputan</span><strong id="user-pickup-fee-label"
                                        data-cart-pickup>Gratis</strong></div>
                                <div class="user-checkout-totals__total"><span>Total estimasi</span><strong
                                        data-cart-total>—</strong></div>
                            </div>

                            <div class="user-address">
                                <div class="user-checkout-label">ALAMAT PENJUAL</div>
                                <p style="font-size:13px; color:#475569; margin-bottom:10px;">Gunakan peta untuk menentukan
                                    lokasi presisi penjemputan.</p>
                                <div id="user-address-fields" style="display:none;">
                                    <label class="user-floating-field">
                                        <textarea rows="3" placeholder=" " id="user-address-input"
                                            name="address"></textarea><span>Alamat lengkap</span>
                                    </label>
                                    <div class="user-address__row">
                                        <label class="user-floating-field"><input type="text" placeholder=" "
                                                id="user-city-input" name="city"><span>Kota</span></label>
                                        <label class="user-floating-field"><input type="text" placeholder=" "
                                                id="user-zip-input" name="zip"><span>Kode pos</span></label>
                                    </div>
                                </div>
                                <label class="user-floating-field" style="margin-top: 10px;">
                                    <textarea rows="2" placeholder=" " id="user-note-input"
                                        name="note"></textarea><span>Tambahkan catatan (cth: pagar cokelat)</span>
                                </label>
                                <button type="button" id="btn-open-user-map"
                                    class="user-button user-button--secondary user-button--full"
                                    style="margin-top: 10px; font-size: 11px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 8px; border: 1px dashed #2563eb; color: #2563eb; background: rgba(37,99,235,0.03);">
                                    <svg viewBox="0 0 24 24"
                                        style="width: 14px; height: 14px; fill: none; stroke: currentColor; stroke-width: 2;">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                        <circle cx="12" cy="10" r="3" />
                                    </svg>
                                    <span>Pilih Lokasi Tepat di Peta (Wajib)</span>
                                </button>
                                <div id="user-latlong-text"
                                    style="display:none; font-size: 11px; color: #475569; margin-top: 8px;"></div>
                                <div id="user-coords-badge"
                                    style="display:none; font-size: 11px; color: #1e40af; background: rgba(37,99,235,0.06); padding: 10px; border-radius: 8px; margin-top: 8px; border: 1px solid rgba(37,99,235,0.15); line-height: 1.4; text-align: left;">
                                    <strong style="display:block; margin-bottom: 2px;">📍 Gudang Penerimaan
                                        Terdekat:</strong>
                                    <span id="nearest-warehouse-detail">Mencari...</span>
                                </div>
                                <p style="margin-top: 10px;">Alamat digunakan untuk menentukan gudang terdekat serta
                                    estimasi biaya penjemputan.</p>
                            </div>

                            <button type="button" id="checkout-submit-btn"
                                class="user-button user-button--primary user-button--full" style="margin-top: 20px;">Lanjut
                                <span aria-hidden="true">→</span></button>
                        </aside>
                    </div>
                </div>
            </section>

            <section class="user-faq user-section" id="faq">
                <div class="user-container user-faq__inner">
                    <div>
                        <span class="user-kicker">PERTANYAAN UMUM</span>
                        <h2>Siap menjual aki<br><em>tanpa proses rumit?</em></h2>
                    </div>
                    <div class="user-faq__list">
                        <details>
                            <summary>Bagaimana cara mengetahui harga aki?</summary>
                            <p>Pilih kota dan jenis aki pada katalog. Nilai yang tampil mengikuti data wilayah yang tersedia
                                di sistem.</p>
                        </details>
                        <details>
                            <summary>Apakah aki harus diantar ke gudang?</summary>
                            <p>Anda dapat memilih untuk mengantar aki ke gudang atau menggunakan layanan
                                penjemputan oleh kurir kami.</p>
                        </details>
                        <details>
                            <summary>Data apa yang perlu disiapkan?</summary>
                            <p>Siapkan identitas penjual, alamat penyerahan, dan rekening bank untuk proses verifikasi serta
                                pembayaran.</p>
                        </details>
                    </div>
                </div>
            </section>
        </main>

        @include('user.partials.footer')
    </div>
    <div id="modal-user-map"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:9999; align-items:center; justify-content:center; padding: 20px;">
        <div
            style="background:#fff; border-radius:12px; width:520px; max-width:100%; box-shadow: 0 10px 25px rgba(0,0,0,0.2); overflow:hidden; border: 1px solid #e2e8f0;">
            <div
                style="padding: 16px 20px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; background:#f8fafc;">
                <h3 style="margin:0; font-size:15px; font-weight:700; color:#1e293b;">📍 Tentukan Lokasi Anda</h3>
                <button type="button" style="border:none; background:none; font-size:18px; cursor:pointer; color:#64748b;"
                    onclick="document.getElementById('modal-user-map').style.display='none'">✕</button>
            </div>
            <div
                style="padding: 12px 20px; font-size:12px; background:#fff; border-bottom:1px solid #e2e8f0; display:flex; gap:8px;">
                <input type="text" id="map-search-input" placeholder="Cari alamat lengkap Anda..."
                    style="flex:1; padding:8px 12px; border:1px solid #cbd5e1; border-radius:6px; font-size:13px;">
                <button type="button" id="btn-map-search" class="user-button user-button--primary"
                    style="padding:8px 16px; border-radius:6px;">Cari</button>
            </div>
            <div
                style="padding: 8px 20px; font-size:11px; color:#64748b; background:#f8fafc; border-bottom:1px solid #e2e8f0; line-height: 1.4;">
                *Apabila pin belum tepat di rumah Anda, mohon bantuannya untuk menggeser pin ke lokasi yang pas.<br>
                <span style="color: #94a3b8;">(Alamat yang diketik akan otomatis tersimpan sebagai alamat
                    pengiriman)</span>
            </div>
            <div id="user-map-picker" style="width:100%; height:320px; background:#e5e7eb;"></div>
            <div
                style="padding: 14px 20px; display:flex; justify-content:flex-end; gap:10px; border-top:1px solid #e2e8f0; background:#f8fafc;">
                <button type="button" class="user-button user-button--secondary"
                    onclick="document.getElementById('modal-user-map').style.display='none'">Batal</button>
                <button type="button" id="btn-save-user-coords" class="user-button user-button--primary"
                    style="background:#2563eb; color:#fff;">Konfirmasi Lokasi</button>
            </div>
        </div>
    </div>
    <div id="modal-user-alert"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:10000; align-items:center; justify-content:center; padding: 20px;">
        <div
            style="background:#fff; border-radius:12px; width:360px; max-width:100%; box-shadow: 0 10px 25px rgba(0,0,0,0.2); overflow:hidden; border: 1px solid #e2e8f0; text-align:center; padding: 24px;">
            <span style="font-size: 40px; display:block; margin-bottom: 12px;">⚠️</span>
            <h3 style="margin:0 0 8px; font-size:16px; font-weight:700; color:#1e293b;">Pemberitahuan</h3>
            <p id="user-alert-message" style="font-size:13px; color:#475569; margin:0 0 18px; line-height:1.5;"></p>
            <button type="button" class="user-button user-button--primary"
                style="background:#2563eb; color:#fff; width:100%; padding: 8px 16px;"
                onclick="document.getElementById('modal-user-alert').style.display='none'">Tutup</button>
        </div>
    </div>

    <a href="#user-checkout-section" id="floating-checkout-btn" class="user-floating-btn"
        title="Lanjutkan ke ringkasan pembelian">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <polyline points="19 12 12 19 5 12"></polyline>
        </svg>
    </a>
@endsection
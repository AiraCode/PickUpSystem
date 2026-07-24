@extends('user.layouts.app')

@section('content')
    <div id="top">
        @include('user.partials.header', ['headerClass' => 'user-header--hero'])

        <main>
            <section class="user-hero">
                <div class="user-hero__media" aria-label="Area gambar hero">
                    <img src="https://images.unsplash.com/photo-1676337167629-d896b3ed5724?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                        alt="Gambar pabrik dan gudang Indoprima belum tersedia (not found)" class="user-hero__image">
                    <span class="user-hero__overlay" aria-hidden="true"></span>
                </div>
                <div class="user-hero__content user-container">
                    <div class="user-hero__copy">
                        <h1>
                            Jual Aki Bekas Anda dengan<br>
                            <em>Harga Terbaik</em>
                        </h1>
                        <p>Indoprima Group membeli aki mobil dan aki motor bekas dengan proses cepat, transparan, dan
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
                        <p>Harga mengikuti wilayah dan kondisi aki. Pilih kota untuk menampilkan data yang terhubung pada sistem.</p>
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
                        <div class="user-catalog-notice" id="user-city-warning" data-city-warning>
                            <strong>*Daftar harga aki berdasarkan wilayah yang dipilih.</strong>
                        </div>
                    </div>

                    <div class="user-catalog-layout">
                        <div class="user-catalog-main">
                            <div class="user-disabled-box" id="user-battery-disabled-placeholder" data-battery-disabled-placeholder>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <strong>*Silakan memilih kota terlebih dahulu</strong>
                                <p>Daftar aki dan informasi harga jual akan muncul secara otomatis setelah Anda menentukan lokasi penyerahan.</p>
                            </div>

                            <div class="user-battery-container" id="user-battery-container" style="display: none;" data-battery-container>
                                <div class="user-store-header">
                                    <div class="user-store-header__title">
                                        <span class="user-store-badge">📍 GUDANG WILAYAH</span>
                                        <strong id="user-store-city-name" data-store-city>SURABAYA</strong>
                                    </div>
                                </div>

                                <div class="user-battery-list">
                                    <div class="user-battery-item" data-product-card data-product-name="GS Astra NS40" data-product-brand="GS Astra" data-product-price="750000">
                                        <div class="user-battery-item__info">
                                            <span class="user-battery-item__tag">AKI MOBIL</span>
                                            <h3>GS Astra NS40 (Aki Mobil 12V 32Ah)</h3>
                                            <p>Kondisi diverifikasi saat penyerahan di gudang.</p>
                                        </div>
                                        <div class="user-battery-item__price">
                                            <span class="user-price-label">Estimasi Harga:</span>
                                            <strong data-product-price-label>Rp 750.000</strong>
                                        </div>
                                        <div class="user-battery-item__actions">
                                            <div class="user-quantity">
                                                <button type="button" data-quantity-minus aria-label="Kurangi jumlah">−</button>
                                                <input type="number" value="1" min="1" max="99" data-quantity aria-label="Jumlah GS Astra NS40">
                                                <button type="button" data-quantity-plus aria-label="Tambah jumlah">+</button>
                                            </div>
                                            <button type="button" class="user-add-button" data-add-to-cart>+ Tambahkan ke Keranjang</button>
                                        </div>
                                    </div>

                                    <div class="user-battery-item" data-product-card data-product-name="Yuasa YBX" data-product-brand="Yuasa" data-product-price="850000">
                                        <div class="user-battery-item__info">
                                            <span class="user-battery-item__tag">AKI MOBIL</span>
                                            <h3>Yuasa YBX (Aki Mobil 12V 45Ah)</h3>
                                            <p>Kondisi diverifikasi saat penyerahan di gudang.</p>
                                        </div>
                                        <div class="user-battery-item__price">
                                            <span class="user-price-label">Estimasi Harga:</span>
                                            <strong data-product-price-label>Rp 850.000</strong>
                                        </div>
                                        <div class="user-battery-item__actions">
                                            <div class="user-quantity">
                                                <button type="button" data-quantity-minus aria-label="Kurangi jumlah">−</button>
                                                <input type="number" value="1" min="1" max="99" data-quantity aria-label="Jumlah Yuasa YBX">
                                                <button type="button" data-quantity-plus aria-label="Tambah jumlah">+</button>
                                            </div>
                                            <button type="button" class="user-add-button" data-add-to-cart>+ Tambahkan ke Keranjang</button>
                                        </div>
                                    </div>

                                    <div class="user-battery-item" data-product-card data-product-name="Incoe MF" data-product-brand="Incoe" data-product-price="250000">
                                        <div class="user-battery-item__info">
                                            <span class="user-battery-item__tag">AKI MOTOR</span>
                                            <h3>Incoe MF (Aki Motor 12V 7Ah)</h3>
                                            <p>Kondisi diverifikasi saat penyerahan di gudang.</p>
                                        </div>
                                        <div class="user-battery-item__price">
                                            <span class="user-price-label">Estimasi Harga:</span>
                                            <strong data-product-price-label>Rp 250.000</strong>
                                        </div>
                                        <div class="user-battery-item__actions">
                                            <div class="user-quantity">
                                                <button type="button" data-quantity-minus aria-label="Kurangi jumlah">−</button>
                                                <input type="number" value="1" min="1" max="99" data-quantity aria-label="Jumlah Incoe MF">
                                                <button type="button" data-quantity-plus aria-label="Tambah jumlah">+</button>
                                            </div>
                                            <button type="button" class="user-add-button" data-add-to-cart>+ Tambahkan ke Keranjang</button>
                                        </div>
                                    </div>

                                    <div class="user-battery-item" data-product-card data-product-name="Delkor 55D23L" data-product-brand="Delkor" data-product-price="950000">
                                        <div class="user-battery-item__info">
                                            <span class="user-battery-item__tag">AKI MOBIL</span>
                                            <h3>Delkor 55D23L (Aki Mobil 12V 60Ah)</h3>
                                            <p>Kondisi diverifikasi saat penyerahan di gudang.</p>
                                        </div>
                                        <div class="user-battery-item__price">
                                            <span class="user-price-label">Estimasi Harga:</span>
                                            <strong data-product-price-label>Rp 950.000</strong>
                                        </div>
                                        <div class="user-battery-item__actions">
                                            <div class="user-quantity">
                                                <button type="button" data-quantity-minus aria-label="Kurangi jumlah">−</button>
                                                <input type="number" value="1" min="1" max="99" data-quantity aria-label="Jumlah Delkor 55D23L">
                                                <button type="button" data-quantity-plus aria-label="Tambah jumlah">+</button>
                                            </div>
                                            <button type="button" class="user-add-button" data-add-to-cart>+ Tambahkan ke Keranjang</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <aside class="user-checkout-card" aria-label="Ringkasan penjualan">
                            <div class="user-checkout-card__head">
                                <div>
                                    <span class="user-kicker">RINGKASAN</span>
                                    <h2>Penjualan Anda</h2>
                                </div>
                                <span class="user-cart-count" data-cart-count>0</span>
                            </div>

                            <div class="user-cart-items" data-cart-items>
                                <div class="user-cart-empty" data-cart-empty>
                                    <span class="user-cart-empty__icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4.5 5.5h15l-1.2 8.5H6.2z" /><path d="M7 5.5 8 3h8l1 2.5M9 18.5h.01M16 18.5h.01" /></svg></span>
                                    <strong>Belum ada aki dipilih</strong>
                                    <p>Tambahkan aki dari daftar untuk melihat rincian.</p>
                                </div>
                            </div>

                            <div class="user-delivery" data-delivery-method>
                                <span class="user-checkout-label">METODE PENYERAHAN AKI</span>
                                <label class="user-radio-card is-selected">
                                    <input type="radio" name="delivery_method" value="warehouse" checked data-pickup-method>
                                    <span class="user-radio-card__indicator"></span>
                                    <span><strong>Antar ke gudang Indoprima</strong><small>Rekomendasi gudang terdekat · biaya layanan gratis.</small></span>
                                </label>
                                <label class="user-radio-card">
                                    <input type="radio" name="delivery_method" value="courier" data-pickup-method>
                                    <span class="user-radio-card__indicator"></span>
                                    <span><strong>Dijemput kurir Indoprima</strong><small>Biaya penjemputan akan dihitung berdasarkan jarak.</small></span>
                                </label>
                            </div>

                            <div class="user-checkout-totals">
                                <div><span>Subtotal</span><strong data-cart-subtotal>—</strong></div>
                                <div><span>Biaya penjemputan</span><strong data-cart-pickup>Gratis</strong></div>
                                <div><span>Potongan</span><strong>—</strong></div>
                                <div class="user-checkout-totals__total"><span>Total estimasi</span><strong data-cart-total>—</strong></div>
                            </div>

                            <div class="user-address">
                                <div class="user-checkout-label">ALAMAT PENJUAL</div>
                                <label class="user-floating-field">
                                    <textarea rows="3" placeholder=" "></textarea><span>Alamat lengkap</span>
                                </label>
                                <div class="user-address__row">
                                    <label class="user-floating-field"><input type="text" placeholder=" "><span>Kota</span></label>
                                    <label class="user-floating-field"><input type="text" placeholder=" "><span>Kode pos</span></label>
                                </div>
                                <p>Alamat digunakan untuk menentukan gudang terdekat serta estimasi biaya penjemputan.</p>
                            </div>

                            <a href="/user/identitas" class="user-button user-button--primary user-button--full" style="margin-top: 20px;">Lanjut Isi Identitas <span aria-hidden="true">→</span></a>
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
                            <p>Pilih kota dan jenis aki pada katalog. Nilai yang tampil mengikuti data wilayah yang tersedia di sistem.</p>
                        </details>
                        <details>
                            <summary>Apakah aki harus diantar ke gudang?</summary>
                            <p>Anda dapat memilih untuk mengantar aki ke gudang Indoprima atau menggunakan layanan penjemputan kurir.</p>
                        </details>
                        <details>
                            <summary>Data apa yang perlu disiapkan?</summary>
                            <p>Siapkan identitas penjual, alamat penyerahan, dan rekening bank untuk proses verifikasi serta pembayaran.</p>
                        </details>
                    </div>
                </div>
            </section>
        </main>

        @include('user.partials.footer')
    </div>
@endsection

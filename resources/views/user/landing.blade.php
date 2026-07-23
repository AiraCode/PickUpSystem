@extends('user.layouts.app')

@section('content')
    <div id="top">
        @include('user.partials.header', ['headerClass' => 'user-header--hero'])

        <main>
            <section class="user-hero">
                <div class="user-hero__media" aria-label="Area gambar hero">
                    <img src="https://images.unsplash.com/photo-1475332658283-d79d63a83da6?w=1920&h=1080&fit=crop&auto=format" alt="Gambar pabrik dan gudang Indoprima belum tersedia (not found)" class="user-hero__image">
                    <span class="user-hero__overlay" aria-hidden="true"></span>
                </div>
                <div class="user-hero__content user-container">
                    <div class="user-hero__copy">
                        <span class="user-badge"><i aria-hidden="true"></i>Membeli aki bekas di seluruh Indonesia</span>
                        <h1>Jual Aki Bekas Anda dengan <em>Harga Terbaik</em></h1>
                        <p>Indoprima Group membeli aki mobil dan aki motor bekas dengan proses cepat, transparan, dan pembayaran langsung ke rekening Anda.</p>
                        <div class="user-hero__actions">
                            <a href="#daftar-harga" class="user-button user-button--primary">Jual Sekarang <span aria-hidden="true">→</span></a>
                            <a href="#daftar-harga" class="user-button user-button--ghost">Lihat Daftar Harga</a>
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
                        <p>Semua proses dibuat sederhana agar Anda dapat mengetahui estimasi nilai aki dan metode penyerahan dengan jelas.</p>
                    </div>
                    <div class="user-process-grid">
                        <article class="user-process-card">
                            <span class="user-process-card__number">01</span>
                            <div class="user-process-card__icon user-process-card__icon--blue"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4.5 6.5h15v12h-15z" /><path d="M8 10h8M8 14h5" /></svg></div>
                            <h3>Pilih aki dan wilayah</h3>
                            <p>Pilih jenis aki dan kota untuk melihat informasi harga yang tersedia pada sistem.</p>
                        </article>
                        <article class="user-process-card">
                            <span class="user-process-card__number">02</span>
                            <div class="user-process-card__icon user-process-card__icon--red"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="3.5" /><path d="M5 20c.8-3.2 3.1-5 7-5s6.2 1.8 7 5" /></svg></div>
                            <h3>Lengkapi identitas</h3>
                            <p>Isi informasi penjual dan rekening yang diperlukan untuk verifikasi serta pembayaran.</p>
                        </article>
                        <article class="user-process-card">
                            <span class="user-process-card__number">03</span>
                            <div class="user-process-card__icon user-process-card__icon--blue"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4.5 7.5h15v11h-15z" /><path d="M8 7.5V5.8h8v1.7M8 12h8M8 15.5h4" /></svg></div>
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
                            <span>Kota penyerahan</span>
                            <select id="user-city" data-city-select>
                                <option value="surabaya">Surabaya</option>
                                <option value="jakarta">Jakarta</option>
                                <option value="bandung">Bandung</option>
                                <option value="semarang">Semarang</option>
                                <option value="yogyakarta">Yogyakarta</option>
                                <option value="makassar">Makassar</option>
                                <option value="palu">Palu</option>
                                <option value="balikpapan">Balikpapan</option>
                            </select>
                        </label>
                        <span class="user-catalog-toolbar__status" data-city-status>Data harga mengikuti wilayah yang dipilih.</span>
                    </div>

                    <div class="user-catalog-layout">
                        <div class="user-product-grid">
                            <article class="user-product-card" data-product-card data-product-name="GS Astra NS40" data-product-brand="GS Astra" data-product-price="0">
                                <div class="user-product-card__image">
                                    <img src="{{ asset('images/not-found/batteries/gs-astra-ns40.jpg') }}" alt="GS Astra NS40 belum tersedia (not found)">
                                    <span class="user-image-not-found">Gambar · not found</span>
                                    <span class="user-product-card__tag">AKI MOBIL</span>
                                </div>
                                <div class="user-product-card__body">
                                    <span class="user-product-card__brand">GS Astra</span>
                                    <h3>GS Astra NS40</h3>
                                    <p>Mobil kompak dan kendaraan keluarga.</p>
                                    <div class="user-product-card__info"><span>Kondisi diverifikasi saat serah terima</span><b>Stok mengikuti data sistem</b></div>
                                    <strong class="user-product-card__price" data-product-price-label>Harga belum tersedia</strong>
                                    <div class="user-product-card__actions">
                                        <div class="user-quantity">
                                            <button type="button" data-quantity-minus aria-label="Kurangi jumlah GS Astra NS40">−</button>
                                            <input type="number" value="1" min="1" max="99" data-quantity aria-label="Jumlah GS Astra NS40">
                                            <button type="button" data-quantity-plus aria-label="Tambah jumlah GS Astra NS40">+</button>
                                        </div>
                                        <button type="button" class="user-add-button" data-add-to-cart>Tambah</button>
                                    </div>
                                </div>
                            </article>

                            <article class="user-product-card" data-product-card data-product-name="Yuasa YBX" data-product-brand="Yuasa" data-product-price="0">
                                <div class="user-product-card__image">
                                    <img src="{{ asset('images/not-found/batteries/yuasa-ybx.jpg') }}" alt="Yuasa YBX belum tersedia (not found)">
                                    <span class="user-image-not-found">Gambar · not found</span>
                                    <span class="user-product-card__tag">AKI MOBIL</span>
                                </div>
                                <div class="user-product-card__body">
                                    <span class="user-product-card__brand">Yuasa</span>
                                    <h3>Yuasa YBX</h3>
                                    <p>Sedan, MPV, dan kendaraan operasional.</p>
                                    <div class="user-product-card__info"><span>Kondisi diverifikasi saat serah terima</span><b>Stok mengikuti data sistem</b></div>
                                    <strong class="user-product-card__price" data-product-price-label>Harga belum tersedia</strong>
                                    <div class="user-product-card__actions">
                                        <div class="user-quantity">
                                            <button type="button" data-quantity-minus aria-label="Kurangi jumlah Yuasa YBX">−</button>
                                            <input type="number" value="1" min="1" max="99" data-quantity aria-label="Jumlah Yuasa YBX">
                                            <button type="button" data-quantity-plus aria-label="Tambah jumlah Yuasa YBX">+</button>
                                        </div>
                                        <button type="button" class="user-add-button" data-add-to-cart>Tambah</button>
                                    </div>
                                </div>
                            </article>

                            <article class="user-product-card" data-product-card data-product-name="Incoe MF" data-product-brand="Incoe" data-product-price="0">
                                <div class="user-product-card__image">
                                    <img src="{{ asset('images/not-found/batteries/incoe-mf.jpg') }}" alt="Incoe MF belum tersedia (not found)">
                                    <span class="user-image-not-found">Gambar · not found</span>
                                    <span class="user-product-card__tag">AKI MOTOR</span>
                                </div>
                                <div class="user-product-card__body">
                                    <span class="user-product-card__brand">Incoe</span>
                                    <h3>Incoe MF</h3>
                                    <p>Motor harian dan kendaraan roda dua.</p>
                                    <div class="user-product-card__info"><span>Kondisi diverifikasi saat serah terima</span><b>Stok mengikuti data sistem</b></div>
                                    <strong class="user-product-card__price" data-product-price-label>Harga belum tersedia</strong>
                                    <div class="user-product-card__actions">
                                        <div class="user-quantity">
                                            <button type="button" data-quantity-minus aria-label="Kurangi jumlah Incoe MF">−</button>
                                            <input type="number" value="1" min="1" max="99" data-quantity aria-label="Jumlah Incoe MF">
                                            <button type="button" data-quantity-plus aria-label="Tambah jumlah Incoe MF">+</button>
                                        </div>
                                        <button type="button" class="user-add-button" data-add-to-cart>Tambah</button>
                                    </div>
                                </div>
                            </article>

                            <article class="user-product-card" data-product-card data-product-name="Delkor 55D23L" data-product-brand="Delkor" data-product-price="0">
                                <div class="user-product-card__image">
                                    <img src="{{ asset('images/not-found/batteries/delkor-55d23l.jpg') }}" alt="Delkor 55D23L belum tersedia (not found)">
                                    <span class="user-image-not-found">Gambar · not found</span>
                                    <span class="user-product-card__tag">AKI MOBIL</span>
                                </div>
                                <div class="user-product-card__body">
                                    <span class="user-product-card__brand">Delkor</span>
                                    <h3>Delkor 55D23L</h3>
                                    <p>Mobil keluarga dan kendaraan niaga ringan.</p>
                                    <div class="user-product-card__info"><span>Kondisi diverifikasi saat serah terima</span><b>Stok mengikuti data sistem</b></div>
                                    <strong class="user-product-card__price" data-product-price-label>Harga belum tersedia</strong>
                                    <div class="user-product-card__actions">
                                        <div class="user-quantity">
                                            <button type="button" data-quantity-minus aria-label="Kurangi jumlah Delkor 55D23L">−</button>
                                            <input type="number" value="1" min="1" max="99" data-quantity aria-label="Jumlah Delkor 55D23L">
                                            <button type="button" data-quantity-plus aria-label="Tambah jumlah Delkor 55D23L">+</button>
                                        </div>
                                        <button type="button" class="user-add-button" data-add-to-cart>Tambah</button>
                                    </div>
                                </div>
                            </article>
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
                                    <p>Tambahkan aki dari katalog untuk membuat ringkasan.</p>
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
                                <label class="user-floating-field"><textarea rows="3" placeholder=" "></textarea><span>Alamat lengkap</span></label>
                                <div class="user-address__row">
                                    <label class="user-floating-field"><input type="text" placeholder=" "><span>Kota</span></label>
                                    <label class="user-floating-field"><input type="text" placeholder=" "><span>Kode pos</span></label>
                                </div>
                                <p>Alamat digunakan untuk menentukan gudang terdekat serta estimasi biaya penjemputan.</p>
                            </div>

                            <a href="/user/identitas" class="user-button user-button--primary user-button--full">Lanjut Isi Identitas <span aria-hidden="true">→</span></a>
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
                        <details open><summary>Bagaimana cara mengetahui harga aki?</summary><p>Pilih kota dan jenis aki pada katalog. Nilai yang tampil mengikuti data wilayah yang tersedia di sistem.</p></details>
                        <details><summary>Apakah aki harus diantar ke gudang?</summary><p>Anda dapat memilih untuk mengantar aki ke gudang Indoprima atau menggunakan layanan penjemputan kurir.</p></details>
                        <details><summary>Data apa yang perlu disiapkan?</summary><p>Siapkan identitas penjual, alamat penyerahan, dan rekening bank untuk proses verifikasi serta pembayaran.</p></details>
                    </div>
                </div>
            </section>
        </main>

        @include('user.partials.footer')
    </div>
@endsection



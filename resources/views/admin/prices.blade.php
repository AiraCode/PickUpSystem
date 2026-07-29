@extends('layouts.admin')

@section('title', 'Harga Aki')

@section('content')
    <div class="admin-page-heading">
        <div>
            <span class="admin-eyebrow">MASTER DATA</span>
            <h1>Harga Aki</h1>
            <p>Kelola data LME, Kurs, area layanan, dan jenis aki beserta persentasenya.</p>
        </div>
    </div>

    <article class="admin-panel" style="margin-bottom: 24px;">
        <div class="admin-panel__head">
            <div>
                <h2>Pengaturan Global LME &amp; Kurs</h2>
                <p style="font-size:12px; color:#6d727c; margin-top:2px;">Rumus Harga: [(LME &times; Kurs &times; % Kota) /
                    1000] &times; Berat Data Kering (kg)</p>
            </div>
        </div>
        <form id="form-global-settings"
            style="display:flex; flex-wrap:wrap; gap:16px; align-items:flex-end; padding-top:8px;">
            <div style="flex:1; min-width:200px;">
                <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">LME
                    (USD/Ton)</label>
                <input type="number" step="any" id="setting-lme" class="admin-select"
                    style="width:100%; padding:8px 12px; border-radius:6px;" placeholder="Contoh: 2100" required>
            </div>
            <div style="flex:1; min-width:200px;">
                <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Kurs
                    (IDR/USD)</label>
                <input type="number" step="any" id="setting-kurs" class="admin-select"
                    style="width:100%; padding:8px 12px; border-radius:6px;" placeholder="Contoh: 16000" required>
            </div>
            <div>
                <button type="submit" class="admin-button admin-button--primary" style="height:38px; padding:0 20px;">Simpan
                    LME &amp; Kurs</button>
            </div>
        </form>
    </article>

    <article class="admin-panel admin-table-panel">
        <div class="admin-panel__head">
            <div>
                <h2>Area Layanan (Kota)</h2>
            </div>
            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <div style="position:relative; width:240px;">
                    <input type="text" id="city-search-input" class="admin-select"
                        style="width:100%; padding:7px 32px 7px 12px; font-size:12px; border-radius:6px; background:#fff;"
                        placeholder="Cari nama kota...">
                    <span
                        style="position:absolute; right:10px; top:50%; transform:translateY(-50%); color:#9ca3af; pointer-events:none;">
                        <svg viewBox="0 0 24 24"
                            style="width:14px; height:14px; fill:none; stroke:currentColor; stroke-width:2;">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                    </span>
                </div>
                <button class="admin-button admin-button--primary" onclick="openAddCityModal()">Tambah Kota</button>
            </div>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nama Kota</th>
                        <th>Persentase (%)</th>
                        <th style="width:180px; text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="cities-tbody">
                    <tr>
                        <td colspan="3">
                            <div class="admin-table-empty"><strong>Memuat data...</strong></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </article>

    <article class="admin-panel admin-table-panel" style="margin-top:20px;">
        <div class="admin-panel__head" style="flex-direction: column; align-items: flex-start; gap: 16px;">
            <div style="display:flex; justify-content: space-between; width: 100%; align-items: center; flex-wrap: wrap; gap: 10px;">
                <h3 style="font-size:16px; margin:0;">Daftar Jenis Aki (<span id="accu-total"></span>)</h3>
                <div style="display:flex; background:#e2e8f0; border-radius:8px; padding:4px; gap:4px; width: fit-content;">
                    <button class="admin-button" id="btn-old-accu" style="background:#fff; color:#1e293b; box-shadow:0 1px 3px rgba(0,0,0,0.1); border:none; padding:6px 16px; border-radius:6px; font-size:13px; font-weight:600;" onclick="switchAccuTab('old')">Aki Bekas (LME)</button>
                    <button class="admin-button" id="btn-new-accu" style="background:transparent; color:#64748b; border:none; padding:6px 16px; border-radius:6px; font-size:13px; font-weight:600; box-shadow:none;" onclick="switchAccuTab('new')">Aki Baru</button>
                </div>
            </div>
        </div>
        
        <!-- Tab Aki Bekas -->
        <div id="tab-old-accu" style="display: block;">
            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin: 16px 20px;">
                <div style="position:relative; width:240px;">
                    <input type="text" id="accu-search-input" class="admin-select"
                        style="width:100%; padding:7px 32px 7px 12px; font-size:12px; border-radius:6px; background:#fff;"
                        placeholder="Cari model, ukuran...">
                    <span
                        style="position:absolute; right:10px; top:50%; transform:translateY(-50%); color:#9ca3af; pointer-events:none;">
                        <svg viewBox="0 0 24 24"
                            style="width:14px; height:14px; fill:none; stroke:currentColor; stroke-width:2;">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                    </span>
                </div>
                <button class="admin-button admin-button--primary"
                    onclick="document.getElementById('modal-add-accu').style.display='flex'">
                    <svg viewBox="0 0 24 24"
                        style="width:16px; height:16px; margin-right:6px; fill:none; stroke:currentColor; stroke-width:2;">
                        <path d="M12 5v14M5 12h14"></path>
                    </svg> Tambah Aki Bekas
                </button>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nama / Tipe Aki</th>
                            <th>Berat Data Kering (kg)</th>
                            <th style="width:140px; text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="accus-tbody">
                        <tr>
                            <td colspan="4">
                                <div class="admin-table-empty"><strong>Memuat data...</strong></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab Aki Baru -->
        <div id="tab-new-accu" style="display: none;">
            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin: 16px 20px;">
                <div style="position:relative; width:240px;">
                    <input type="text" id="new-accu-search-input" class="admin-select"
                        style="width:100%; padding:7px 32px 7px 12px; font-size:12px; border-radius:6px; background:#fff;"
                        placeholder="Cari nama aki baru...">
                    <span
                        style="position:absolute; right:10px; top:50%; transform:translateY(-50%); color:#9ca3af; pointer-events:none;">
                        <svg viewBox="0 0 24 24"
                            style="width:14px; height:14px; fill:none; stroke:currentColor; stroke-width:2;">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                    </span>
                </div>
                <button class="admin-button admin-button--primary"
                    onclick="openAddNewAccuModal()">
                    <svg viewBox="0 0 24 24"
                        style="width:16px; height:16px; margin-right:6px; fill:none; stroke:currentColor; stroke-width:2;">
                        <path d="M12 5v14M5 12h14"></path>
                    </svg> Tambah Aki Baru
                </button>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nama Aki</th>
                            <th>Merk</th>
                            <th>Harga Jual Baru (Rp)</th>
                            <th style="width:140px; text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="new-accus-tbody">
                        <tr>
                            <td colspan="4">
                                <div class="admin-table-empty"><strong>Memuat data...</strong></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </article>

    <article class="admin-panel admin-table-panel" style="margin-top:20px;">
        <div class="admin-panel__head">
            <div>
                <h2>Riwayat Perubahan Parameter</h2>
                <p style="font-size:12px; color:#6d727c; margin-top:2px;">Catatan log historis perubahan LME, Kurs, dan
                    persentase kota.</p>
            </div>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:200px;">Tanggal / Waktu</th>
                        <th>LME</th>
                        <th>Kurs</th>
                    </tr>
                </thead>
                <tbody id="price-history-tbody">
                    <tr>
                        <td colspan="5">
                            <div class="admin-table-empty"><strong>Memuat riwayat...</strong></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </article>

    <div id="modal-view-city-prices"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:100; align-items:center; justify-content:center;">
        <div class="admin-panel" style="width:780px; max-width:92vw; max-height:90vh; overflow-y:auto; overflow-x:hidden;">
            <div class="admin-panel__head" style="align-items: flex-start;">
                <div>
                    <h2 id="city-price-modal-title">Daftar Harga Aki Kota</h2>
                    <p style="font-size:12px; color:#6d727c; margin-top:2px;">Atur persentase (%) untuk kota ini di bagian
                        atas. Harga aki dihitung otomatis dengan rumus.</p>
                </div>
                <button type="button" class="admin-button admin-button--secondary"
                    style="padding: 6px 12px; height: 34px; white-space: nowrap; flex-shrink: 0;"
                    onclick="document.getElementById('modal-view-city-prices').style.display='none'">Tutup</button>
            </div>

            <form id="form-city-detail-percentage"
                style="display:flex; align-items:center; gap:12px; background:#f8fafc; border:1px solid #e2e8f0; padding:12px 16px; border-radius:8px; margin-top:10px;">
                <label style="font-size:13px; font-weight:700; color:#374151; white-space:nowrap;">Persentase Kota
                    (%):</label>
                <input type="number" step="0.1" min="0" max="100" id="city-detail-percentage-input" class="admin-select"
                    style="width:110px; padding:6px 10px; border-radius:6px; font-weight:700; color:#2563eb;" required>
                <button type="submit" class="admin-button admin-button--primary"
                    style="height:34px; font-size:12px; white-space:nowrap;">Simpan Persentase</button>
            </form>

            <div class="admin-table-wrap" style="margin-top:14px; overflow-x:hidden;">
                <table class="admin-table" style="width:100%;">
                    <thead>
                        <tr>
                            <th style="width:35%;">Nama Aki</th>
                            <th style="width:20%;">Berat Kering</th>
                            <th style="width:20%; text-align:right;">Harga Aki (Rp)</th>
                        </tr>
                    </thead>
                    <tbody id="modal-city-accus-tbody">
                        <tr>
                            <td colspan="4">
                                <div class="admin-table-empty"><strong>Memuat data harga...</strong></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <div id="modal-add-city"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:100; align-items:center; justify-content:center;">
        <div class="admin-panel" style="width:420px;">
            <div class="admin-panel__head">
                <h2>Tambah Kota</h2>
            </div>
            <form id="form-add-city">
                <div style="margin-bottom:14px;">
                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Nama
                        Kota</label>
                    <input type="text" id="city-name" class="admin-select"
                        style="width:100%; padding:8px 10px; border-radius:6px;" placeholder="Contoh: Surabaya" required>
                </div>
                <div style="margin-bottom:16px;">
                    <label
                        style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Persentase
                        Kota (%)</label>
                    <input type="number" step="0.1" min="0" max="100" id="city-percentage" class="admin-select"
                        style="width:100%; padding:8px 10px; border-radius:6px;" value="80" required>
                </div>
                <div id="trashed-cities-container"
                    style="margin-bottom:16px; border-top:1px solid #e5e7eb; padding-top:12px;">
                    <label style="display:block; font-size:12px; font-weight:600; color:#6d727c; margin-bottom:6px;">Riwayat
                        Kota Terhapus (Klik untuk pulihkan)</label>
                    <div id="trashed-cities-list" style="max-height:100px; overflow-y:auto; font-size:12px;">
                        <span style="color:#9ca3af;">Memuat...</span>
                    </div>
                </div>
                <div style="display:flex; gap:10px; justify-content:flex-end;">
                    <button type="button" class="admin-button admin-button--secondary"
                        onclick="document.getElementById('modal-add-city').style.display='none'">Batal</button>
                    <button type="submit" class="admin-button admin-button--primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modal-add-accu"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:100; align-items:center; justify-content:center;">
        <div class="admin-panel" style="width:440px; max-width:92vw;">
            <div class="admin-panel__head">
                <h2>Tambah Aki Baru</h2>
                <p style="font-size:12px; color:#6d727c; margin-top:2px;">Aki baru akan otomatis terhubung dan berlaku di
                    semua kota.</p>
            </div>
            <form id="form-add-accu">

                <div style="margin-bottom:14px;">
                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Nama /
                        Tipe Aki</label>
                    <input type="text" id="accu-name" class="admin-select"
                        style="width:100%; padding:8px 10px; border-radius:6px;" placeholder="Contoh: NS40, NS60, N50Z"
                        required>
                </div>
                <div style="margin-bottom:18px;">
                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Berat
                        Data Kering (kg)</label>
                    <input type="number" step="0.01" id="accu-berat-kering" class="admin-select"
                        style="width:100%; padding:8px 10px; border-radius:6px;" placeholder="Contoh: 5.50" required>
                </div>

                <div id="trashed-accus-container"
                    style="margin-bottom:16px; border-top:1px solid #e5e7eb; padding-top:12px;">
                    <label style="display:block; font-size:12px; font-weight:600; color:#6d727c; margin-bottom:6px;">Riwayat
                        Aki Terhapus (Klik untuk pulihkan)</label>
                    <div id="trashed-accus-list" style="max-height:100px; overflow-y:auto; font-size:12px;">
                        <span style="color:#9ca3af;">Memuat...</span>
                    </div>
                </div>
                <div style="display:flex; gap:10px; justify-content:flex-end;">
                    <button type="button" class="admin-button admin-button--secondary"
                        onclick="document.getElementById('modal-add-accu').style.display='none'">Batal</button>
                    <button type="submit" class="admin-button admin-button--primary">Simpan Aki</button>
                </div>
            </form>
        </div>
    </div>
    <div id="modal-add-new-accu"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:100; align-items:center; justify-content:center;">
        <div class="admin-panel" style="width:440px; max-width:92vw;">
            <div class="admin-panel__head">
                <h2 id="modal-new-accu-title">Tambah Aki Baru</h2>
            </div>
            <form id="form-add-new-accu">
                <input type="hidden" id="new-accu-id">
                
                <div style="margin-bottom:14px;">
                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Nama / Tipe Aki</label>
                    <input type="text" id="new-accu-name" class="admin-select"
                        style="width:100%; padding:8px 10px; border-radius:6px;" placeholder="Contoh: GS Astra NS40" required>
                </div>
                
                <div style="margin-bottom:14px;">
                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Merk Aki</label>
                    <select id="new-accu-brand" class="admin-select" style="width:100%; padding:8px 10px; border-radius:6px;" required>
                        <option value="">Pilih Merk</option>
                    </select>
                </div>

                <div style="margin-bottom:18px;">
                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Harga Jual Baru (Rp)</label>
                    <input type="number" id="new-accu-price" class="admin-select"
                        style="width:100%; padding:8px 10px; border-radius:6px;" placeholder="Contoh: 850000" required>
                </div>

                <div style="display:flex; gap:10px; justify-content:flex-end;">
                    <button type="button" class="admin-button admin-button--secondary"
                        onclick="document.getElementById('modal-add-new-accu').style.display='none'">Batal</button>
                    <button type="submit" class="admin-button admin-button--primary">Simpan Aki Baru</button>
                </div>
            </form>
        </div>
    </div>
@endsection
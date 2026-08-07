@extends('layouts.admin')

@section('title', 'Gudang & Lokasi')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="admin-page-heading">
    <div>
        <span class="admin-eyebrow">MASTER DATA</span>
        <h1>Gudang & Lokasi</h1>
        <p>Kelola data titik layanan warehouse Pick Up System.</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; margin-bottom: 20px;" id="storage-dashboard-summary">
    <article class="admin-panel admin-stat-card" style="border:2px solid #dbeafe;">
        <div class="admin-stat-card__head" style="margin-bottom:6px;">
            <span class="admin-stat-card__icon" style="background:#bfdbfe; color:#1e40af; padding:6px; border-radius:6px;">
                <svg viewBox="0 0 24 24" style="width:20px; height:20px; fill:none; stroke:currentColor; stroke-width:2;">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </span>
        </div>
        <p style="margin:0; font-size:12px; color:#1e40af; font-weight:600;">Total Aki Sudah Diambil</p>
        <strong id="count-total-taken" style="font-size:22px; color:#1e3a8a;">0</strong>
        <small style="color:#3b82f6; font-size:11px;">Akumulasi seluruh gudang</small>
    </article>
    <article class="admin-panel admin-stat-card" style="border:2px solid #fee2e2;">
        <div class="admin-stat-card__head" style="margin-bottom:6px;">
            <span class="admin-stat-card__icon" style="background:#fecaca; color:#991b1b; padding:6px; border-radius:6px;">
                <svg viewBox="0 0 24 24" style="width:20px; height:20px; fill:none; stroke:currentColor; stroke-width:2;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </span>
        </div>
        <p style="margin:0; font-size:12px; color:#991b1b; font-weight:600;">Total Aki Belum Diambil</p>
        <strong id="count-total-untaken" style="font-size:22px; color:#7f1d1d;">0</strong>
        <small style="color:#ef4444; font-size:11px;">Tersebar di cabang (masih di gudang)</small>
    </article>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
    <!-- Left Column: Daftar Gudang -->
    <div style="display:flex; flex-direction:column; gap:20px; width:100%;">
        <article class="admin-panel admin-table-panel" style="width:100%;">
            <div class="admin-panel__head">
                <div><h2>Daftar Gudang</h2></div>
                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                    <div style="position:relative; width:240px;">
                        <input type="text" id="storage-search-input" class="admin-select"
                            style="width:100%; padding:7px 32px 7px 12px; font-size:12px; border-radius:6px; background:#fff;"
                            placeholder="Cari nama gudang, alamat...">
                        <span
                            style="position:absolute; right:10px; top:50%; transform:translateY(-50%); color:#9ca3af; pointer-events:none;">
                            <svg viewBox="0 0 24 24"
                                style="width:14px; height:14px; fill:none; stroke:currentColor; stroke-width:2;">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                        </span>
                    </div>
                    <button class="admin-button admin-button--primary" onclick="openAddStorageModal()">Tambah Gudang</button>
                </div>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nama Gudang</th>
                            <th>Alamat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="storages-tbody">
                        <tr><td colspan="3"><div class="admin-table-empty">Memuat data...</div></td></tr>
                    </tbody>
                </table>
            </div>
            <div id="storages-pagination" class="admin-pagination"></div>
        </article>
    </div>

    <!-- Right Column: Menunggu Pengambilan -->
    <div id="ready-pickup-container" style="display: flex; flex-direction: column; gap: 12px;">
        <h3 style="font-size: 15px; margin-top:0; margin-bottom: 4px; color: #111827;">Menunggu Pengambilan Pusat</h3>
        <p style="margin:0 0 12px 0; font-size:12px; color:#6b7280;">Daftar gudang cabang dengan jumlah stok &ge; 20 unit.</p>
        <div id="ready-pickup-list" style="display:flex; flex-direction:column; gap:12px;">
            <!-- Cards JS Rendered Here -->
            <div style="padding:15px; border:1px solid #e5e7eb; border-radius:8px; background:#f9fafb; text-align:center;">
                <small style="color:#6b7280;">Memuat status pengambilan...</small>
            </div>
        </div>
    </div>
</div>

<div id="modal-add-storage" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:100; align-items:center; justify-content:center;">
    <div class="admin-panel" style="width:520px;">
        <div class="admin-panel__head"><h2>Tambah Gudang</h2></div>
        <form id="form-add-storage">
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Nama Gudang</label>
                <input type="text" id="storage-name" class="admin-select" style="width:100%; padding:8px 10px; border-radius:6px;" placeholder="Contoh: Gudang Utama Surabaya" required>
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Alamat Lengkap</label>
                <input type="text" id="storage-address" class="admin-select" style="width:100%; padding:8px 10px; border-radius:6px;" placeholder="Contoh: Jl. Raya Darmo No.12" required>
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Pilih Lokasi di Peta</label>
                <p style="font-size:11px; color:#6d727c; margin-bottom:8px;">Klik pada peta untuk menandai titik lokasi gudang.</p>
                <div id="map-add" style="height:250px; border-radius:8px; border:1px solid #e5e7eb; z-index:1;"></div>
                <input type="hidden" id="storage-lat" required>
                <input type="hidden" id="storage-long" required>
                <p id="map-coords" style="font-size:11px; color:#6d727c; margin-top:6px; text-align:center;">Belum ada titik dipilih</p>
            </div>
            <div id="trashed-storages-container" style="margin-bottom:16px; border-top:1px solid #e5e7eb; padding-top:12px;">
                <label style="display:block; font-size:12px; font-weight:600; color:#6d727c; margin-bottom:6px;">Riwayat Gudang Terhapus (Klik untuk pulihkan)</label>
                <div id="trashed-storages-list" style="max-height:100px; overflow-y:auto; font-size:12px;">
                    <span style="color:#9ca3af;">Memuat...</span>
                </div>
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" class="admin-button admin-button--secondary" onclick="document.getElementById('modal-add-storage').style.display='none'">Batal</button>
                <button type="submit" class="admin-button admin-button--primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="modal-delete-storage" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:100; align-items:center; justify-content:center;">
    <div class="admin-panel" style="width:400px;">
        <div class="admin-panel__head"><h2 style="color:#ba1b2b;">Konfirmasi Hapus Gudang</h2></div>
        <form id="form-delete-storage">
            <input type="hidden" id="delete-storage-id">
            <p style="font-size:13px; color:#4a4f59; margin-bottom:14px;">Masukkan kata sandi akun Admin Anda untuk mengonfirmasi penghapusan gudang ini.</p>
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Kata Sandi Admin</label>
                <input type="password" id="delete-storage-password" class="admin-select" style="width:100%; padding:8px 10px; border-radius:6px;" placeholder="Masukkan kata sandi Anda" required>
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" class="admin-button admin-button--secondary" onclick="document.getElementById('modal-delete-storage').style.display='none'">Batal</button>
                <button type="submit" class="admin-button admin-button--primary" style="background:#ba1b2b; border-color:#ba1b2b;">Hapus Gudang</button>
            </div>
        </form>
    </div>
</div>

<div id="modal-view-map" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:100; align-items:center; justify-content:center;">
    <div class="admin-panel" style="width:550px;">
        <div class="admin-panel__head" style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
            <h2 id="view-map-title">Lokasi Gudang</h2>
            <button type="button" class="admin-button admin-button--primary" style="padding:6px 12px; height:34px;" onclick="document.getElementById('modal-view-map').style.display='none'">Tutup</button>
        </div>
        <div id="map-view" style="height:350px; border-radius:8px; border:1px solid #e5e7eb; z-index:1;"></div>

    </div>
</div>
@endsection


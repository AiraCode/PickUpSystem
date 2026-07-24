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

<article class="admin-panel admin-table-panel">
    <div class="admin-panel__head">
        <div><h2>Daftar Gudang</h2></div>
        <button class="admin-button admin-button--primary" onclick="openAddStorageModal()">Tambah Gudang</button>
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
                <tr><td colspan="3"><div class="admin-table-empty"><strong>Memuat data...</strong></div></td></tr>
            </tbody>
        </table>
    </div>
</article>

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

<!-- Modal Konfirmasi Hapus Gudang (Meminta Password Admin) -->
<div id="modal-delete-storage" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:100; align-items:center; justify-content:center;">
    <div class="admin-panel" style="width:400px;">
        <div class="admin-panel__head"><h2 style="color:#ba1b2b;">Konfirmasi Hapus Gudang</h2></div>
        <form id="form-delete-storage">
            <input type="hidden" id="delete-storage-id">
            <p style="font-size:13px; color:#4a4f59; margin-bottom:14px;">Masukkan password akun Admin Anda untuk mengonfirmasi penghapusan gudang ini.</p>
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Password Admin</label>
                <input type="password" id="delete-storage-password" class="admin-select" style="width:100%; padding:8px 10px; border-radius:6px;" placeholder="Masukkan password Anda" required>
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
        <div class="admin-panel__head"><h2 id="view-map-title">Lokasi Gudang</h2></div>
        <div id="map-view" style="height:350px; border-radius:8px; border:1px solid #e5e7eb; z-index:1;"></div>
        <div style="display:flex; justify-content:flex-end; margin-top:12px;">
            <button type="button" class="admin-button admin-button--primary" onclick="document.getElementById('modal-view-map').style.display='none'">Tutup</button>
        </div>
    </div>
</div>
@endsection

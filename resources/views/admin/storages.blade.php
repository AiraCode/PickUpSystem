@extends('layouts.admin')

@section('title', 'Gudang & Lokasi')

@section('content')
<div class="admin-page-heading">
    <div>
        <span class="admin-eyebrow">MASTER DATA</span>
        <h1>Gudang & Lokasi</h1>
        <p>Kelola data titik layanan warehouse Pick Up System.</p>
    </div>
</div>

<article class="admin-panel admin-table-panel">
    <div class="admin-panel__head">
        <div>
            <h2>Daftar Gudang</h2>
        </div>
        <button class="admin-button admin-button--primary" onclick="document.getElementById('modal-add-storage').style.display='flex'">Tambah Gudang</button>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Gudang</th>
                    <th>Kota ID</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="storages-tbody">
                <tr>
                    <td colspan="4">
                        <div class="admin-table-empty">
                            <strong>Memuat data...</strong>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</article>

<!-- Modal Tambah Gudang -->
<div id="modal-add-storage" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center;">
    <div class="admin-panel" style="width: 400px;">
        <div class="admin-panel__head">
            <h2>Tambah Gudang</h2>
        </div>
        <form id="form-add-storage">
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-size:12px; font-weight:bold; margin-bottom:5px;">ID Gudang (Angka)</label>
                <input type="number" id="storage-id" class="admin-select" style="width: 100%; padding: 5px;" required>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-size:12px; font-weight:bold; margin-bottom:5px;">Nama Gudang</label>
                <input type="text" id="storage-name" class="admin-select" style="width: 100%; padding: 5px;" required>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-size:12px; font-weight:bold; margin-bottom:5px;">ID Kota (Angka)</label>
                <input type="number" id="storage-city-id" class="admin-select" style="width: 100%; padding: 5px;" required>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="admin-button admin-button--secondary" onclick="document.getElementById('modal-add-storage').style.display='none'">Batal</button>
                <button type="submit" class="admin-button admin-button--primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

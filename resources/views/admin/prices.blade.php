@extends('layouts.admin')

@section('title', 'Harga Aki')

@section('content')
<div class="admin-page-heading">
    <div>
        <span class="admin-eyebrow">MASTER DATA</span>
        <h1>Harga Aki</h1>
        <p>Kelola data kota, jenis aki, dan atur harga per kota.</p>
    </div>
</div>

<article class="admin-panel admin-table-panel">
    <div class="admin-panel__head">
        <div><h2>Area Layanan (Kota)</h2></div>
        <button class="admin-button admin-button--primary" onclick="document.getElementById('modal-add-city').style.display='flex'">Tambah Kota</button>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nama Kota</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="cities-tbody">
                <tr><td colspan="2"><div class="admin-table-empty"><strong>Memuat data...</strong></div></td></tr>
            </tbody>
        </table>
    </div>
</article>

<article class="admin-panel admin-table-panel" style="margin-top:20px;">
    <div class="admin-panel__head">
        <div><h2>Daftar Jenis Aki</h2></div>
        <button class="admin-button admin-button--primary" onclick="document.getElementById('modal-add-accu').style.display='flex'">Tambah Aki</button>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Brand</th>
                    <th>Nama Aki</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="accus-tbody">
                <tr><td colspan="3"><div class="admin-table-empty"><strong>Memuat data...</strong></div></td></tr>
            </tbody>
        </table>
    </div>
</article>

<article class="admin-panel admin-table-panel" style="margin-top:20px;">
    <div class="admin-panel__head">
        <div>
            <h2>Harga Aki Per Kota</h2>
            <p style="font-size:12px; color:#6d727c; margin-top:4px;">Klik "Lihat Harga" pada tabel kota di atas, atau gunakan tombol Atur Harga.</p>
        </div>
        <button class="admin-button admin-button--primary" onclick="document.getElementById('modal-set-price').style.display='flex'">Atur Harga</button>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Kota</th>
                    <th>Brand</th>
                    <th>Nama Aki</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="city-accus-tbody">
                <tr><td colspan="5"><div class="admin-table-empty"><strong>Pilih kota untuk melihat harga</strong></div></td></tr>
            </tbody>
        </table>
    </div>
</article>

<div id="modal-add-city" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:100; align-items:center; justify-content:center;">
    <div class="admin-panel" style="width:420px;">
        <div class="admin-panel__head"><h2>Tambah Kota</h2></div>
        <form id="form-add-city">
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Nama Kota</label>
                <input type="text" id="city-name" class="admin-select" style="width:100%; padding:8px 10px; border-radius:6px;" placeholder="Contoh: Surabaya" required>
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" class="admin-button admin-button--secondary" onclick="document.getElementById('modal-add-city').style.display='none'">Batal</button>
                <button type="submit" class="admin-button admin-button--primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="modal-add-accu" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:100; align-items:center; justify-content:center;">
    <div class="admin-panel" style="width:420px;">
        <div class="admin-panel__head"><h2>Tambah Aki</h2></div>
        <form id="form-add-accu">
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Brand / Merk</label>
                <input type="text" id="accu-brand" class="admin-select" style="width:100%; padding:8px 10px; border-radius:6px;" placeholder="Contoh: GS Astra, Yuasa" required>
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Nama / Tipe Aki</label>
                <input type="text" id="accu-name" class="admin-select" style="width:100%; padding:8px 10px; border-radius:6px;" placeholder="Contoh: NS40, NS60" required>
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" class="admin-button admin-button--secondary" onclick="document.getElementById('modal-add-accu').style.display='none'">Batal</button>
                <button type="submit" class="admin-button admin-button--primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="modal-set-price" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:100; align-items:center; justify-content:center;">
    <div class="admin-panel" style="width:420px;">
        <div class="admin-panel__head"><h2>Atur Harga Aki</h2></div>
        <form id="form-set-price">
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Pilih Kota</label>
                <select id="set-price-city" class="admin-select" style="width:100%; padding:8px 10px; border-radius:6px;" required></select>
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Pilih Aki</label>
                <select id="set-price-accu" class="admin-select" style="width:100%; padding:8px 10px; border-radius:6px;" required></select>
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Harga (Rp)</label>
                <input type="number" id="set-price-value" class="admin-select" style="width:100%; padding:8px 10px; border-radius:6px;" placeholder="Contoh: 150000" required>
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" class="admin-button admin-button--secondary" onclick="document.getElementById('modal-set-price').style.display='none'">Batal</button>
                <button type="submit" class="admin-button admin-button--primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

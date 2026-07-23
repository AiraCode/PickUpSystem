@extends('layouts.admin')

@section('title', 'Harga Aki')

@section('content')
<div class="admin-page-heading">
    <div>
        <span class="admin-eyebrow">MASTER DATA</span>
        <h1>Harga Aki</h1>
        <p>Kelola data kota dan jenis aki beserta harganya.</p>
    </div>
</div>

<div class="admin-dashboard-grid admin-dashboard-grid--main">
    <article class="admin-panel">
        <div class="admin-panel__head">
            <div>
                <h2>Daftar Kota (Area Layanan)</h2>
            </div>
            <button class="admin-button admin-button--primary" onclick="document.getElementById('modal-add-city').style.display='flex'">Tambah Kota</button>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Kota</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="cities-tbody">
                    <tr>
                        <td colspan="3">
                            <div class="admin-table-empty">
                                <strong>Memuat data...</strong>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </article>

    <article class="admin-panel">
        <div class="admin-panel__head">
            <div>
                <h2>Daftar Jenis Aki</h2>
            </div>
            <button class="admin-button admin-button--primary" onclick="document.getElementById('modal-add-accu').style.display='flex'">Tambah Aki</button>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Aki</th>
                        <th>Base Price</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="accus-tbody">
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
</div>

<!-- Modal Tambah Kota -->
<div id="modal-add-city" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center;">
    <div class="admin-panel" style="width: 400px;">
        <div class="admin-panel__head">
            <h2>Tambah Kota</h2>
        </div>
        <form id="form-add-city">
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-size:12px; font-weight:bold; margin-bottom:5px;">ID Kota (Angka)</label>
                <input type="number" id="city-id" class="admin-select" style="width: 100%; padding: 5px;" required>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-size:12px; font-weight:bold; margin-bottom:5px;">Nama Kota</label>
                <input type="text" id="city-name" class="admin-select" style="width: 100%; padding: 5px;" required>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="admin-button admin-button--secondary" onclick="document.getElementById('modal-add-city').style.display='none'">Batal</button>
                <button type="submit" class="admin-button admin-button--primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Aki -->
<div id="modal-add-accu" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center;">
    <div class="admin-panel" style="width: 400px;">
        <div class="admin-panel__head">
            <h2>Tambah Aki</h2>
        </div>
        <form id="form-add-accu">
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-size:12px; font-weight:bold; margin-bottom:5px;">ID Aki (Angka)</label>
                <input type="number" id="accu-id" class="admin-select" style="width: 100%; padding: 5px;" required>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-size:12px; font-weight:bold; margin-bottom:5px;">Nama Aki</label>
                <input type="text" id="accu-name" class="admin-select" style="width: 100%; padding: 5px;" required>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="admin-button admin-button--secondary" onclick="document.getElementById('modal-add-accu').style.display='none'">Batal</button>
                <button type="submit" class="admin-button admin-button--primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Harga Aki')

@section('content')
    <div class="admin-page-heading">
        <div>
            <span class="admin-eyebrow">MASTER DATA</span>
            <h1>Harga Aki</h1>
            <p>Kelola data kota dan jenis aki beserta pengaturannya.</p>
        </div>
    </div>

    <article class="admin-panel admin-table-panel">
        <div class="admin-panel__head">
            <div>
                <h2>Area Layanan (Kota)</h2>
            </div>
            <button class="admin-button admin-button--primary"
                onclick="document.getElementById('modal-add-city').style.display='flex'">Tambah Kota</button>
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
                    <tr>
                        <td colspan="2">
                            <div class="admin-table-empty"><strong>Memuat data...</strong></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </article>

    <article class="admin-panel admin-table-panel" style="margin-top:20px;">
        <div class="admin-panel__head">
            <div>
                <h2>Daftar Jenis Aki</h2>
            </div>
            <button class="admin-button admin-button--primary"
                onclick="document.getElementById('modal-add-accu').style.display='flex'">Tambah Aki</button>
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
                    <tr>
                        <td colspan="3">
                            <div class="admin-table-empty"><strong>Memuat data...</strong></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </article>

    <!-- Modal Pop-Up Daftar Harga Aki Per Kota -->
    <div id="modal-view-city-prices"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:100; align-items:center; justify-content:center;">
        <div class="admin-panel" style="width:740px; max-width:92vw; max-height:90vh; overflow-y:auto; overflow-x:hidden;">
            <div class="admin-panel__head"
                style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
                <div>
                    <h2 id="city-price-modal-title">Daftar Harga Aki Kota</h2>
                    <p style="font-size:12px; color:#6d727c; margin-top:2px;">Atur dan edit harga aki khusus untuk area
                        layanan ini.</p>
                </div>
                <button class="admin-button admin-button--primary" style="height:32px; font-size:12px; white-space:nowrap;"
                    onclick="openAddPriceForCurrentCity()">Tambah Aki</button>
            </div>
            <div class="admin-table-wrap" style="margin-top:12px; overflow-x:hidden;">
                <table class="admin-table" style="width:100%;">
                    <thead>
                        <tr>
                            <th style="width:22%;">Brand</th>
                            <th style="width:25%;">Nama Aki</th>
                            <th style="width:25%;">Harga (Rp)</th>
                            <th style="width:28%; text-align:right;">Aksi</th>
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
            <div style="display:flex; justify-content:flex-end; margin-top:16px;">
                <button type="button" class="admin-button admin-button--secondary"
                    onclick="document.getElementById('modal-view-city-prices').style.display='none'">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Kota -->
    <div id="modal-add-city"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:100; align-items:center; justify-content:center;">
        <div class="admin-panel" style="width:420px;">
            <div class="admin-panel__head">
                <h2>Tambah Kota</h2>
            </div>
            <form id="form-add-city">
                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Nama
                        Kota</label>
                    <input type="text" id="city-name" class="admin-select"
                        style="width:100%; padding:8px 10px; border-radius:6px;" placeholder="Contoh: Surabaya" required>
                </div>
                <div style="display:flex; gap:10px; justify-content:flex-end;">
                    <button type="button" class="admin-button admin-button--secondary"
                        onclick="document.getElementById('modal-add-city').style.display='none'">Batal</button>
                    <button type="submit" class="admin-button admin-button--primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah Aki (Brand berupa Dropdown Select) -->
    <div id="modal-add-accu"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:100; align-items:center; justify-content:center;">
        <div class="admin-panel" style="width:420px;">
            <div class="admin-panel__head">
                <h2>Tambah Aki Baru</h2>
            </div>
            <form id="form-add-accu">
                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Pilih
                        Brand / Merk</label>
                    <select id="accu-brand" class="admin-select" style="width:100%; padding:8px 10px; border-radius:6px;"
                        required>
                        <option value="" disabled selected>-- Pilih Brand Aki --</option>
                        <option value="GS Astra">GS Astra</option>
                        <option value="Yuasa">Yuasa</option>
                        <option value="Incoe">Incoe</option>
                        <option value="Delkor">Delkor</option>
                        <option value="Massiv">Massiv</option>
                        <option value="Amaron">Amaron</option>
                        <option value="Bosch">Bosch</option>
                        <option value="Rocket">Rocket</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Nama /
                        Tipe Aki</label>
                    <input type="text" id="accu-name" class="admin-select"
                        style="width:100%; padding:8px 10px; border-radius:6px;" placeholder="Contoh: NS40, NS60, N50Z"
                        required>
                </div>
                <div style="display:flex; gap:10px; justify-content:flex-end;">
                    <button type="button" class="admin-button admin-button--secondary"
                        onclick="document.getElementById('modal-add-accu').style.display='none'">Batal</button>
                    <button type="submit" class="admin-button admin-button--primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Atur / Edit Harga Aki -->
    <div id="modal-set-price"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:110; align-items:center; justify-content:center;">
        <div class="admin-panel" style="width:420px;">
            <div class="admin-panel__head">
                <h2 id="set-price-modal-head">Atur Harga Aki</h2>
            </div>
            <form id="form-set-price">
                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Kota
                        Layanan</label>
                    <select id="set-price-city" class="admin-select"
                        style="width:100%; padding:8px 10px; border-radius:6px;" required></select>
                </div>
                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Tipe
                        Aki</label>
                    <select id="set-price-accu" class="admin-select"
                        style="width:100%; padding:8px 10px; border-radius:6px;" required></select>
                </div>
                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Harga
                        Aki (Rp)</label>
                    <input type="number" id="set-price-value" class="admin-select"
                        style="width:100%; padding:8px 10px; border-radius:6px;" placeholder="Contoh: 150000" required>
                </div>
                <div style="display:flex; gap:10px; justify-content:flex-end;">
                    <button type="button" class="admin-button admin-button--secondary"
                        onclick="document.getElementById('modal-set-price').style.display='none'">Batal</button>
                    <button type="submit" class="admin-button admin-button--primary">Simpan Harga</button>
                </div>
            </form>
        </div>
    </div>
@endsection
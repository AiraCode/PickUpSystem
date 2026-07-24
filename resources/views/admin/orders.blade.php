@extends('layouts.admin')

@section('title', 'Transaksi Masuk')

@section('content')
<div class="admin-page-heading">
    <div>
        <span class="admin-eyebrow">OPERASIONAL & MANAJEMEN</span>
        <h1>Transaksi Masuk</h1>
        <p>Kelola dan perbarui status pesanan dari pelanggan secara real-time.</p>
    </div>
</div>

<!-- Simple KPI Dashboard & Status Filter Tabs (Horizontal Grid Layout) -->
<div style="display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 16px; margin-bottom: 20px;">
    <!-- Pending Card (Default Active) -->
    <article id="card-status-pending" class="admin-panel admin-stat-card order-status-tab active" onclick="switchOrderTab('pending')" style="cursor:pointer; border:2px solid #f59e0b; background:#fffbeb; transition:all 0.2s;">
        <div class="admin-stat-card__head" style="margin-bottom:6px;">
            <span class="admin-stat-card__icon" style="background:#fef3c7; color:#d97706; padding:6px; border-radius:6px;">
                <svg viewBox="0 0 24 24" style="width:20px; height:20px; fill:none; stroke:currentColor; stroke-width:2;"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            </span>
        </div>
        <p style="margin:0; font-size:12px; color:#92400e; font-weight:600;">Pending</p>
        <strong id="count-pending" style="font-size:22px; color:#b45309;">0</strong>
        <small style="color:#d97706; font-size:11px;">Menunggu verifikasi</small>
    </article>

    <!-- Processing Card -->
    <article id="card-status-processing" class="admin-panel admin-stat-card order-status-tab" onclick="switchOrderTab('processing')" style="cursor:pointer; border:2px solid #e5e7eb; transition:all 0.2s;">
        <div class="admin-stat-card__head" style="margin-bottom:6px;">
            <span class="admin-stat-card__icon" style="background:#dbeafe; color:#2563eb; padding:6px; border-radius:6px;">
                <svg viewBox="0 0 24 24" style="width:20px; height:20px; fill:none; stroke:currentColor; stroke-width:2;"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
            </span>
        </div>
        <p style="margin:0; font-size:12px; color:#4b5563; font-weight:600;">Processing</p>
        <strong id="count-processing" style="font-size:22px; color:#1d4ed8;">0</strong>
        <small style="color:#6b7280; font-size:11px;">Sedang dijemput</small>
    </article>

    <!-- Completed Card -->
    <article id="card-status-completed" class="admin-panel admin-stat-card order-status-tab" onclick="switchOrderTab('completed')" style="cursor:pointer; border:2px solid #e5e7eb; transition:all 0.2s;">
        <div class="admin-stat-card__head" style="margin-bottom:6px;">
            <span class="admin-stat-card__icon" style="background:#d1fae5; color:#059669; padding:6px; border-radius:6px;">
                <svg viewBox="0 0 24 24" style="width:20px; height:20px; fill:none; stroke:currentColor; stroke-width:2;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </span>
        </div>
        <p style="margin:0; font-size:12px; color:#4b5563; font-weight:600;">Completed</p>
        <strong id="count-completed" style="font-size:22px; color:#047857;">0</strong>
        <small style="color:#6b7280; font-size:11px;">Transaksi selesai</small>
    </article>

    <!-- Cancelled Card -->
    <article id="card-status-cancelled" class="admin-panel admin-stat-card order-status-tab" onclick="switchOrderTab('cancelled')" style="cursor:pointer; border:2px solid #e5e7eb; transition:all 0.2s;">
        <div class="admin-stat-card__head" style="margin-bottom:6px;">
            <span class="admin-stat-card__icon" style="background:#fee2e2; color:#dc2626; padding:6px; border-radius:6px;">
                <svg viewBox="0 0 24 24" style="width:20px; height:20px; fill:none; stroke:currentColor; stroke-width:2;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </span>
        </div>
        <p style="margin:0; font-size:12px; color:#4b5563; font-weight:600;">Cancelled</p>
        <strong id="count-cancelled" style="font-size:22px; color:#b91c1c;">0</strong>
        <small style="color:#6b7280; font-size:11px;">Pesanan dibatalkan</small>
    </article>

    <!-- All Card -->
    <article id="card-status-all" class="admin-panel admin-stat-card order-status-tab" onclick="switchOrderTab('all')" style="cursor:pointer; border:2px solid #e5e7eb; transition:all 0.2s;">
        <div class="admin-stat-card__head" style="margin-bottom:6px;">
            <span class="admin-stat-card__icon" style="background:#f3f4f6; color:#4b5563; padding:6px; border-radius:6px;">
                <svg viewBox="0 0 24 24" style="width:20px; height:20px; fill:none; stroke:currentColor; stroke-width:2;"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </span>
        </div>
        <p style="margin:0; font-size:12px; color:#4b5563; font-weight:600;">Semua Transaksi</p>
        <strong id="count-all" style="font-size:22px; color:#374151;">0</strong>
        <small style="color:#6b7280; font-size:11px;">Tampilkan seluruh data</small>
    </article>
</div>

<article class="admin-panel admin-table-panel">
    <div class="admin-panel__head" style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:12px;">
        <div style="display:flex; align-items:center; gap:10px;">
            <h2>Daftar Pesanan</h2>
            <span id="active-tab-badge" style="padding:4px 10px; border-radius:20px; font-size:11px; font-weight:700; background:#fef3c7; color:#92400e; text-transform:uppercase;">
                PENDING
            </span>
        </div>

        <!-- Search Bar & Multi-Criteria Shopee-Style Filter Controls -->
        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
            <!-- Search Input (Global search across ALL statuses) -->
            <div style="position:relative; width:260px;">
                <input type="text" id="order-search-input" class="admin-select" style="width:100%; padding:7px 32px 7px 12px; font-size:12px; border-radius:6px; background:#fff;" placeholder="Cari nama, ID, kota, hp, alamat...">
                <span style="position:absolute; right:10px; top:50%; transform:translateY(-50%); color:#9ca3af; pointer-events:none;">
                    <svg viewBox="0 0 24 24" style="width:14px; height:14px; fill:none; stroke:currentColor; stroke-width:2;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </span>
            </div>

            <!-- Shopee-Style Advanced Filter Button -->
            <button id="btn-open-filter-modal" class="admin-button admin-button--secondary" style="height:32px; font-size:12px; padding:0 12px; display:inline-flex; align-items:center; gap:6px; background:#fff; border:1px solid #d1d5db; color:#374151; font-weight:600;">
                <svg viewBox="0 0 24 24" style="width:15px; height:15px; fill:none; stroke:#2563eb; stroke-width:2;"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                <span>Filter Lanjutan</span>
                <span id="filter-active-count" style="display:none; background:#2563eb; color:#fff; font-size:10px; padding:1px 6px; border-radius:10px; font-weight:700;">0</span>
            </button>

            <!-- Reset Filter Button -->
            <button id="btn-reset-order-filter" class="admin-button admin-button--secondary" style="height:32px; font-size:11px; padding:0 10px;" title="Reset Filter">
                Reset
            </button>
        </div>
    </div>

    <!-- Active Filter Badges Container -->
    <div id="active-filters-bar" style="display:none; padding:8px 16px; background:#f8fafc; border-bottom:1px solid #e5e7eb; gap:8px; align-items:center; flex-wrap:wrap; font-size:12px;">
        <span style="font-weight:600; color:#64748b;">Filter Aktif:</span>
        <div id="active-filter-tags" style="display:flex; gap:6px; flex-wrap:wrap;"></div>
    </div>

    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width:80px;">ID Order</th>
                    <th>Pelanggan</th>
                    <th>Kota</th>
                    <th>Alamat Penjemputan</th>
                    <th>Waktu Pesan</th>
                    <th>Status</th>
                    <th style="text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody id="orders-tbody">
                <tr><td colspan="7"><div class="admin-table-empty"><strong>Memuat data...</strong></div></td></tr>
            </tbody>
        </table>
    </div>
</article>

<!-- Modal Filter Lanjutan (Shopee Style) -->
<div id="modal-shopee-filter" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:120; align-items:center; justify-content:center;">
    <div class="admin-panel" style="width:480px;">
        <div class="admin-panel__head" style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e5e7eb; padding-bottom:12px; margin-bottom:16px;">
            <div style="display:flex; align-items:center; gap:8px;">
                <svg viewBox="0 0 24 24" style="width:18px; height:18px; fill:none; stroke:#2563eb; stroke-width:2;"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                <h2 style="font-size:16px; margin:0;">Filter Transaksi Lanjutan</h2>
            </div>
            <button type="button" class="admin-button admin-button--secondary" style="height:28px; font-size:11px; padding:0 8px;" onclick="document.getElementById('modal-shopee-filter').style.display='none'">✕</button>
        </div>
        
        <form id="form-shopee-filter">
            <div style="display:flex; flex-direction:column; gap:14px; margin-bottom:20px;">
                <!-- Filter Kota -->
                <div>
                    <label style="display:block; font-size:12px; font-weight:700; color:#374151; margin-bottom:6px;">📍 Kota Layanan</label>
                    <select id="filter-city-select" class="admin-select" style="width:100%; padding:8px 10px; border-radius:6px; font-size:12px; background:#fff;">
                        <option value="">Semua Kota</option>
                    </select>
                </div>

                <!-- Filter Bank Pelanggan -->
                <div>
                    <label style="display:block; font-size:12px; font-weight:700; color:#374151; margin-bottom:6px;">🏦 Bank Pelanggan</label>
                    <select id="filter-bank-select" class="admin-select" style="width:100%; padding:8px 10px; border-radius:6px; font-size:12px; background:#fff;">
                        <option value="">Semua Bank</option>
                    </select>
                </div>

                <!-- Filter Rentang Tanggal -->
                <div>
                    <label style="display:block; font-size:12px; font-weight:700; color:#374151; margin-bottom:6px;">📅 Rentang Tanggal Pesanan</label>
                    <div style="display:flex; gap:10px; align-items:center;">
                        <input type="date" id="filter-date-start" class="admin-select" style="flex:1; padding:7px 10px; font-size:12px; border-radius:6px; background:#fff;">
                        <span style="font-size:12px; color:#6b7280;">s/d</span>
                        <input type="date" id="filter-date-end" class="admin-select" style="flex:1; padding:7px 10px; font-size:12px; border-radius:6px; background:#fff;">
                    </div>
                </div>

                <!-- Urutan Sort -->
                <div>
                    <label style="display:block; font-size:12px; font-weight:700; color:#374151; margin-bottom:6px;">🔀 Urutkan Berdasarkan Waktu</label>
                    <select id="filter-sort-select" class="admin-select" style="width:100%; padding:8px 10px; border-radius:6px; font-size:12px; background:#fff;">
                        <option value="desc">Terbaru ke Terlama (Default)</option>
                        <option value="asc">Terlama ke Terbaru</option>
                    </select>
                </div>
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end; border-top:1px solid #e5e7eb; padding-top:14px;">
                <button type="button" id="btn-reset-shopee-filter" class="admin-button admin-button--secondary">Reset Filter</button>
                <button type="submit" class="admin-button admin-button--primary" style="background:#2563eb;">Terapkan Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Update Status -->
<div id="modal-update-order" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:100; align-items:center; justify-content:center;">
    <div class="admin-panel" style="width:460px;">
        <div class="admin-panel__head"><h2>Update Status Pesanan</h2></div>
        <form id="form-update-order">
            <input type="hidden" id="update-order-id">
            <div style="margin-bottom:18px;">
                <label style="display:block; font-size:12px; font-weight:600; margin-bottom:10px; color:#4a4f59;">Pilih Status Baru</label>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <label style="display:flex; align-items:center; gap:10px; padding:10px 14px; border:2px solid #e5e7eb; border-radius:8px; cursor:pointer; transition:border-color .15s;">
                        <input type="radio" name="order_status" value="pending" style="accent-color:#f59e0b; width:16px; height:16px;">
                        <div>
                            <span style="font-weight:600; font-size:13px; color:#111318;">Pending</span>
                            <span style="display:block; font-size:11px; color:#6d727c;">Pesanan masuk, menunggu diproses</span>
                        </div>
                    </label>
                    <label style="display:flex; align-items:center; gap:10px; padding:10px 14px; border:2px solid #e5e7eb; border-radius:8px; cursor:pointer; transition:border-color .15s;">
                        <input type="radio" name="order_status" value="processing" style="accent-color:#3b82f6; width:16px; height:16px;">
                        <div>
                            <span style="font-weight:600; font-size:13px; color:#111318;">Processing</span>
                            <span style="display:block; font-size:11px; color:#6d727c;">Sedang dijemput / diproses admin</span>
                        </div>
                    </label>
                    <label style="display:flex; align-items:center; gap:10px; padding:10px 14px; border:2px solid #e5e7eb; border-radius:8px; cursor:pointer; transition:border-color .15s;">
                        <input type="radio" name="order_status" value="completed" style="accent-color:#10b981; width:16px; height:16px;">
                        <div>
                            <span style="font-weight:600; font-size:13px; color:#111318;">Completed</span>
                            <span style="display:block; font-size:11px; color:#6d727c;">Pesanan selesai (wajib upload foto bukti)</span>
                        </div>
                    </label>
                    <label style="display:flex; align-items:center; gap:10px; padding:10px 14px; border:2px solid #e5e7eb; border-radius:8px; cursor:pointer; transition:border-color .15s;">
                        <input type="radio" name="order_status" value="cancelled" style="accent-color:#ef4444; width:16px; height:16px;">
                        <div>
                            <span style="font-weight:600; font-size:13px; color:#111318;">Cancelled</span>
                            <span style="display:block; font-size:11px; color:#6d727c;">Pesanan dibatalkan (wajib beri alasan)</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Field Alasan Pembatalan -->
            <div id="container-cancel-reason" style="display:none; margin-bottom:18px;">
                <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#ba1b2b;">Alasan Pembatalan (Wajib)</label>
                <input type="text" id="cancel-reason" class="admin-select" style="width:100%; padding:8px 10px; border-radius:6px;" placeholder="Contoh: Barang tidak sesuai / Customer membatalkan">
            </div>

            <div style="margin-bottom:18px;">
                <label id="proof-label" style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Bukti Pembayaran / Penyerahan</label>
                <div id="upload-area" style="border:2px dashed #d1d5db; border-radius:8px; padding:20px; text-align:center; cursor:pointer; transition:border-color .15s;">
                    <input type="file" id="upload-proof" accept="image/*" style="display:none;">
                    <div id="upload-placeholder">
                        <svg viewBox="0 0 24 24" style="width:32px; height:32px; fill:none; stroke:#9ca3af; stroke-width:1.5; margin:0 auto 8px;"><path d="M12 16V4m0 0L8 8m4-4 4 4"/><path d="M20 16v2a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2"/></svg>
                        <p style="font-size:12px; color:#6d727c; margin:0;">Klik untuk upload foto atau seret file ke sini</p>
                    </div>
                    <img id="upload-preview" style="display:none; max-width:100%; max-height:150px; border-radius:6px; margin:0 auto;">
                </div>
            </div>
            <div id="order-update-error" style="display:none; background:#fee2e2; color:#991b1b; padding:10px 14px; border-radius:6px; font-size:12px; font-weight:600; margin-bottom:16px;"></div>
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" class="admin-button admin-button--secondary" onclick="document.getElementById('modal-update-order').style.display='none'">Batal</button>
                <button type="submit" class="admin-button admin-button--primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Detail Order -->
<div id="modal-detail-order" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:100; align-items:center; justify-content:center;">
    <div class="admin-panel" style="width:540px; max-height:90vh; overflow-y:auto;">
        <div class="admin-panel__head"><h2>Detail Transaksi</h2></div>
        <div style="padding-bottom:20px;">
            <div style="margin-bottom:18px; border-bottom:1px solid #e5e7eb; padding-bottom:14px;">
                <h3 style="font-size:14px; margin-bottom:12px; color:#111318;">Informasi Pelanggan</h3>
                <table style="width:100%; font-size:13px; border-collapse:collapse;">
                    <tr><td style="width:35%; color:#6d727c; padding:6px 0;">Nama Lengkap</td><td id="detail-customer-name" style="font-weight:500;"></td></tr>
                    <tr><td style="color:#6d727c; padding:6px 0;">Nomor HP</td><td id="detail-customer-phone" style="font-weight:500;"></td></tr>
                    <tr><td style="color:#6d727c; padding:6px 0;">Alamat Lengkap</td><td id="detail-customer-address" style="font-weight:500;"></td></tr>
                    <tr><td style="color:#6d727c; padding:6px 0;">No. KTP</td><td id="detail-customer-ktp" style="font-weight:500;"></td></tr>
                    <tr><td style="color:#6d727c; padding:6px 0;">Bank & Rekening</td><td id="detail-customer-bank" style="font-weight:500;"></td></tr>
                </table>
            </div>
            <div>
                <h3 style="font-size:14px; margin-bottom:12px; color:#111318;">Informasi Pesanan</h3>
                <table style="width:100%; font-size:13px; border-collapse:collapse;">
                    <tr><td style="width:35%; color:#6d727c; padding:6px 0;">Kota Layanan</td><td id="detail-order-city" style="font-weight:500;"></td></tr>
                    <tr><td style="color:#6d727c; padding:6px 0;">Status</td><td id="detail-order-status" style="font-weight:500;"></td></tr>
                    <tr><td style="color:#6d727c; padding:6px 0;">Waktu Pesan</td><td id="detail-order-time" style="font-weight:500;"></td></tr>
                    <tr>
                        <td style="color:#6d727c; padding:6px 0; vertical-align:top;">Alamat Penjemputan</td>
                        <td>
                            <div style="display:flex; flex-direction:column; gap:6px;">
                                <span id="detail-order-pickup-address" style="font-weight:500;"></span>
                                <div>
                                    <button type="button" id="btn-open-order-map" class="admin-button admin-button--primary" style="height:28px; padding:0 12px; font-size:11px; display:inline-flex; align-items:center; gap:5px; background:#2563eb;">
                                        <svg viewBox="0 0 24 24" style="width:13px; height:13px; fill:none; stroke:currentColor; stroke-width:2;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                        Lihat Alamat di Map
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr><td style="color:#6d727c; padding:6px 0;" id="detail-order-note-label">Catatan Alamat</td><td id="detail-order-pickup-note" style="font-weight:500;"></td></tr>
                </table>
            </div>
        </div>
        <div style="display:flex; justify-content:flex-end; padding-top:10px; border-top:1px solid #e5e7eb;">
            <button type="button" class="admin-button admin-button--primary" onclick="document.getElementById('modal-detail-order').style.display='none'">Tutup</button>
        </div>
    </div>
</div>

<!-- Modal Interactive Map Penjemputan (Leaflet OpenStreetMap) -->
<div id="modal-order-map" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:150; align-items:center; justify-content:center;">
    <div class="admin-panel" style="width:620px; max-width:95vw;">
        <div class="admin-panel__head" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <div>
                <h2>📍 Lokasi Penjemputan Barang</h2>
                <p id="map-modal-subtitle" style="font-size:12px; color:#6d727c; margin:2px 0 0;"></p>
            </div>
            <button type="button" class="admin-button admin-button--secondary" style="height:28px; font-size:11px;" onclick="document.getElementById('modal-order-map').style.display='none'">Tutup Map</button>
        </div>
        <div style="margin-bottom:10px; font-size:12px; color:#374151; background:#f8fafc; padding:8px 12px; border-radius:6px; border:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
            <span><strong>Koordinat GPS:</strong> <span id="map-modal-coords" style="font-family:monospace; color:#2563eb; font-weight:700;">-</span></span>
            <span id="map-modal-city-badge" style="padding:2px 8px; border-radius:12px; background:#dbeafe; color:#1e40af; font-weight:600; font-size:11px;">-</span>
        </div>
        <div id="order-map-view" style="width:100%; height:350px; border-radius:8px; border:1px solid #e5e7eb; position:relative;"></div>
    </div>
</div>
@endsection

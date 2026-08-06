@extends('layouts.admin')

@section('title', 'Detail Gudang & Stok Aki')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="admin-page-heading" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
    <div>
        <span class="admin-eyebrow">MANAJEMEN GUDANG</span>
        <h1 id="warehouse-title-heading">Detail Gudang...</h1>
        <p id="warehouse-address-heading">Kelola dan lihat informasi stok aki yang tersimpan di gudang ini.</p>
    </div>
    <a href="/admin/gudang" class="admin-button admin-button--secondary" style="display:inline-flex; align-items:center; gap:6px; height:36px; text-decoration:none;">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        <span>Kembali ke Daftar Gudang</span>
    </a>
</div>

<div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; margin-bottom: 20px;">
    <article class="admin-panel admin-stat-card">
        <div class="admin-stat-card__head" style="margin-bottom:6px;">
            <span class="admin-stat-card__icon" style="background:#dbeafe; color:#2563eb; padding:6px; border-radius:6px;">
                🏢
            </span>
        </div>
        <p style="margin:0; font-size:12px; color:#4b5563; font-weight:600;">Nama Gudang</p>
        <strong id="stat-warehouse-name" style="font-size:18px; color:#1e293b;">-</strong>
        <small id="stat-warehouse-address" style="color:#64748b; font-size:11px; display:block; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;">-</small>
    </article>

    <article class="admin-panel admin-stat-card">
        <div class="admin-stat-card__head" style="margin-bottom:6px;">
            <span class="admin-stat-card__icon" style="background:#d1fae5; color:#059669; padding:6px; border-radius:6px;">
                ⚡
            </span>
        </div>
        <p style="margin:0; font-size:12px; color:#065f46; font-weight:600;">Total Stok Aki Tersimpan</p>
        <strong id="stat-total-items" style="font-size:22px; color:#059669;">0</strong>
        <small style="color:#10b981; font-size:11px;">Aki dari pesanan yang sudah diterima di gudang</small>
    </article>

    <article class="admin-panel admin-stat-card">
        <div class="admin-stat-card__head" style="margin-bottom:6px;">
            <span class="admin-stat-card__icon" style="background:#fef3c7; color:#d97706; padding:6px; border-radius:6px;">
                📍
            </span>
        </div>
        <p style="margin:0; font-size:12px; color:#92400e; font-weight:600;">Koordinat Lokasi</p>
        <strong id="stat-coords" style="font-size:16px; color:#b45309; font-family:monospace;">-</strong>
        <small style="color:#d97706; font-size:11px;">Titik GPS lokasi gudang</small>
    </article>
</div>

<div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; align-items:stretch;">
    {{-- Panel Peta Gudang --}}
    <article class="admin-panel" style="display:flex; flex-direction:column;">
        <div class="admin-panel__head" style="margin-bottom:12px;">
            <h2>📍 Peta Lokasi Gudang</h2>
        </div>
        <div id="detail-warehouse-map" style="height:420px; flex:1; min-height:380px; border-radius:8px; border:1px solid #e5e7eb; z-index:1;"></div>
    </article>

    {{-- Panel Stok Barang --}}
    <article class="admin-panel admin-table-panel" style="display:flex; flex-direction:column;">
        <div class="admin-panel__head" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:12px;">
            <div>
                <h2>📦 Stok Barang per Jenis Aki</h2>
                <p style="font-size:12px; color:#64748b; margin:2px 0 0;">Daftar akumulasi aki (diurutkan berdasarkan jumlah terbesar).</p>
            </div>
            <div style="display:flex; align-items:center; gap:8px;">
                <input type="text" id="input-search-warehouse-stock" class="admin-select" placeholder="🔍 Cari jenis / nama..." style="padding:5px 10px; font-size:12px; width:170px; border-radius:6px; background:#fff; border:1px solid #cbd5e1;">
                <span id="stock-badge-count" style="padding:3px 8px; border-radius:20px; font-size:11px; font-weight:700; background:#dbeafe; color:#1e40af; white-space:nowrap;">
                    0 Jenis Aki
                </span>
            </div>
        </div>
        <div class="admin-table-wrap" style="max-height:310px; overflow-y:auto; overflow-x:hidden; flex:1;">
            <table style="width:100%; border-collapse:collapse; font-size:12px;">
                <thead>
                    <tr style="border-bottom:1px solid #e2e8f0; background:#f8fafc; color:#64748b; font-size:11px; text-transform:uppercase;">
                        <th style="padding:8px 10px; text-align:left; font-weight:700;">Jenis Aki</th>
                        <th style="padding:8px 10px; text-align:center; font-weight:700; width:80px;">Jumlah</th>
                        <th style="padding:8px 10px; text-align:right; font-weight:700; width:90px;">Status</th>
                    </tr>
                </thead>
                <tbody id="storage-stocks-tbody">
                    <tr><td colspan="3"><div class="admin-table-empty"><strong>Memuat data stok gudang...</strong></div></td></tr>
                </tbody>
            </table>
        </div>
    </article>
</div>

<script>
document.addEventListener("DOMContentLoaded", async () => {
    const warehouseId = {{ $id }};
    const titleHeading = document.getElementById("warehouse-title-heading");
    const addressHeading = document.getElementById("warehouse-address-heading");
    const statName = document.getElementById("stat-warehouse-name");
    const statAddr = document.getElementById("stat-warehouse-address");
    const statTotal = document.getElementById("stat-total-items");
    const statCoords = document.getElementById("stat-coords");
    const stocksTbody = document.getElementById("storage-stocks-tbody");
    const stockBadgeCount = document.getElementById("stock-badge-count");
    const searchInput = document.getElementById("input-search-warehouse-stock");

    let map = null;
    let allStocksData = [];

    function renderStockTable(filteredStocks) {
        if (!stocksTbody) return;
        if (filteredStocks.length > 0) {
            stocksTbody.innerHTML = filteredStocks.map(item => {
                const qty = parseInt(item.total_quantity || 0);
                const badgeHtml = qty > 0
                    ? `<span style="background:#d1fae5; color:#065f46; font-size:10px; font-weight:700; padding:2px 8px; border-radius:12px; display:inline-block;">Tersedia</span>`
                    : `<span style="background:#f1f5f9; color:#64748b; font-size:10px; font-weight:700; padding:2px 8px; border-radius:12px; display:inline-block;">Stok 0</span>`;

                return `
                    <tr style="border-bottom:1px solid #f1f5f9; ${qty > 0 ? 'background:#ecfdf5;' : ''}">
                        <td style="padding:8px 10px; font-weight:700; color:#1e293b; font-size:12px;">
                            ⚡ ${item.accu_name || "-"}
                        </td>
                        <td style="padding:8px 10px; text-align:center; font-weight:700; color:${qty > 0 ? '#059669' : '#64748b'}; font-size:12px; white-space:nowrap;">
                            ${qty} unit
                        </td>
                        <td style="padding:8px 10px; text-align:right;">
                            ${badgeHtml}
                        </td>
                    </tr>
                `;
            }).join("");
        } else {
            stocksTbody.innerHTML = `<tr><td colspan="3"><div class="admin-table-empty"><strong>Tidak ada jenis aki yang cocok</strong></div></td></tr>`;
        }
    }

    try {
        const headers = { "Accept": "application/json" };
        const token = localStorage.getItem("admin_token");
        if (token) headers["Authorization"] = `Bearer ${token}`;

        const res = await fetch(`/api/admin/storages/${warehouseId}/stock`, { headers });

        if (!res.ok) throw new Error("Gagal mengambil data gudang");
        const data = await res.json();
        const w = data.warehouse;
        const stocks = data.stocks || [];
        const totalItems = data.total_items || 0;

        titleHeading.innerText = `Detail ${w.name}`;
        addressHeading.innerText = w.address || "Alamat gudang belum diisi.";
        statName.innerText = w.name;
        statAddr.innerText = w.address || "-";
        statTotal.innerText = totalItems.toLocaleString("id-ID") + " Unit";
        statCoords.innerText = `${parseFloat(w.lat).toFixed(4)}, ${parseFloat(w.long).toFixed(4)}`;
        stockBadgeCount.innerText = `${stocks.length} Jenis Aki`;

        // Render Map
        const lat = parseFloat(w.lat || -7.250445);
        const lng = parseFloat(w.long || 112.768845);

        if (document.getElementById("detail-warehouse-map")) {
            map = L.map("detail-warehouse-map").setView([lat, lng], 14);
            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                attribution: "&copy; OpenStreetMap contributors"
            }).addTo(map);

            L.marker([lat, lng]).addTo(map)
                .bindPopup(`<strong style="font-size:13px; color:#2563eb;">📍 ${w.name}</strong><br><span style="font-size:11px; color:#4b5563;">${w.address || ''}</span>`)
                .openPopup();

            setTimeout(() => {
                if (map) map.invalidateSize();
            }, 300);
        }

        // Sort Stocks: highest quantity first, then alphabetically
        allStocksData = stocks.sort((a, b) => {
            const qtyA = parseInt(a.total_quantity || 0);
            const qtyB = parseInt(b.total_quantity || 0);
            if (qtyB !== qtyA) {
                return qtyB - qtyA;
            }
            return (a.accu_name || "").localeCompare(b.accu_name || "");
        });

        renderStockTable(allStocksData);

        if (searchInput) {
            searchInput.addEventListener("input", (e) => {
                const query = e.target.value.toLowerCase().trim();
                const filtered = allStocksData.filter(item =>
                    (item.accu_name && item.accu_name.toLowerCase().includes(query)) ||
                    (item.accu_brand && item.accu_brand.toLowerCase().includes(query))
                );
                renderStockTable(filtered);
                stockBadgeCount.innerText = `${filtered.length} Jenis Aki`;
            });
        }

    } catch (e) {
        console.error(e);
        stocksTbody.innerHTML = `<tr><td colspan="3"><div class="admin-table-empty" style="color:#dc2626;"><strong>Terjadi kesalahan saat memuat stok gudang</strong></div></td></tr>`;
    }
});
</script>
@endsection

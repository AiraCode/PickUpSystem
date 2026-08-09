@extends('layouts.admin')

@section('title', 'Detail Gudang & Stok Aki')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="admin-page-heading" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
    <div>
        <span class="admin-eyebrow">MANAJEMEN GUDANG</span>
        <h1 id="warehouse-title-heading">Detail Gudang</h1>
        <p id="warehouse-address-heading">Kelola dan lihat informasi stok aki yang tersimpan di gudang ini.</p>
    </div>
    <a href="/admin/gudang" class="admin-button admin-button--secondary" style="display:inline-flex; align-items:center; gap:6px; height:36px; text-decoration:none;">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        <span>Kembali ke Daftar Gudang</span>
    </a>
</div>

<div class="admin-grid-4col">
    <article class="admin-panel admin-stat-card storage-stat-card">
        <div class="admin-stat-card__head" style="margin-bottom:6px;">
            <span class="admin-stat-card__icon stat-icon--blue">
                🏢
            </span>
        </div>
        <p class="stat-card-label">Nama Gudang</p>
        <strong id="stat-warehouse-name" class="stat-card-value">-</strong>
        <small id="stat-warehouse-address" class="stat-card-desc">-</small>
    </article>

    <article class="admin-panel admin-stat-card storage-stat-card">
        <div class="admin-stat-card__head" style="margin-bottom:6px;">
            <span class="admin-stat-card__icon stat-icon--green">
                ⚡
            </span>
        </div>
        <p class="stat-card-label stat-card-label--green">Total Stok Aki Tersimpan</p>
        <strong id="stat-total-items" class="stat-card-value stat-card-value--green">0</strong>
        <small class="stat-card-desc stat-card-desc--green">Aki dari pesanan yang sudah diterima di gudang</small>
    </article>

    <article class="admin-panel admin-stat-card storage-stat-card">
        <div class="admin-stat-card__head" style="margin-bottom:6px;">
            <span class="admin-stat-card__icon stat-icon--indigo">
                📦
            </span>
        </div>
        <p class="stat-card-label stat-card-label--indigo">Aki Dikirim ke Pusat</p>
        <strong id="stat-total-taken" class="stat-card-value stat-card-value--indigo">0</strong>
        <small class="stat-card-desc stat-card-desc--indigo">Aki yang dikirim ke gudang utama</small>
    </article>

    <article class="admin-panel admin-stat-card storage-stat-card">
        <div class="admin-stat-card__head" style="margin-bottom:6px;">
            <span class="admin-stat-card__icon stat-icon--amber">
                📍
            </span>
        </div>
        <p class="stat-card-label stat-card-label--amber">Koordinat Lokasi</p>
        <strong id="stat-coords" class="stat-card-value stat-card-value--amber" style="font-family:monospace;">-</strong>
        <small class="stat-card-desc stat-card-desc--amber">Titik GPS lokasi gudang</small>
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
                <p class="admin-panel-subdesc">Daftar akumulasi aki (diurutkan berdasarkan jumlah terbesar).</p>
            </div>
            <div style="display:flex; align-items:center; gap:8px;">
                <input type="text" id="input-search-warehouse-stock" class="admin-select" placeholder="🔍 Cari jenis / nama..." style="padding:5px 10px; font-size:12px; width:170px; border-radius:6px;">
                <span id="stock-badge-count" class="stock-badge-pill">
                    0 Jenis Aki
                </span>
            </div>
        </div>
        <div class="admin-table-wrap" style="max-height:380px; overflow-y:auto; overflow-x:hidden; flex:1;">
            <table class="admin-table stock-table" style="width:100%; border-collapse:collapse; font-size:12px;">
                <thead>
                    <tr>
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
    const statTotalTaken = document.getElementById("stat-total-taken");
    const stocksTbody = document.getElementById("storage-stocks-tbody");
    const stockBadgeCount = document.getElementById("stock-badge-count");
    const searchInput = document.getElementById("input-search-warehouse-stock");

    let map = null;
    let allStocksData = [];
    let allTakenStocksData = [];

    function renderStockTable(filteredStocks) {
        if (!stocksTbody) return;
        const validStocks = filteredStocks.filter(item => item.accu_name && item.accu_name.trim() !== "");
        if (validStocks.length > 0) {
            stocksTbody.innerHTML = validStocks.map(item => {
                const qty = parseInt(item.total_quantity || 0);
                const badgeHtml = qty > 0
                    ? `<span class="admin-badge admin-badge--success">Tersedia</span>`
                    : `<span class="admin-badge admin-badge--muted">Stok 0</span>`;

                return `
                    <tr class="stock-table-row ${qty > 0 ? 'stock-table-row--active' : ''}">
                        <td class="stock-table-name">
                            ⚡ ${item.accu_name}
                        </td>
                        <td class="stock-table-qty ${qty > 0 ? 'stock-table-qty--positive' : ''}">
                            ${qty} unit
                        </td>
                        <td class="stock-table-status">
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
        const stocks = (data.stocks || []).filter(item => item.accu_name && item.accu_name.trim() !== "");
        const takenStocks = (data.taken_stocks || []).filter(item => item.accu_name && item.accu_name.trim() !== "");
        const totalItems = data.total_items || 0;
        const totalTakenItems = data.total_taken_items || 0;

        titleHeading.innerText = `Detail ${w.name}`;
        addressHeading.innerText = w.address || "Alamat gudang belum diisi.";
        statName.innerText = w.name;
        statAddr.innerText = w.address || "-";
        statTotal.innerText = totalItems.toLocaleString("id-ID") + " Unit";
        statCoords.innerText = `${parseFloat(w.lat).toFixed(4)}, ${parseFloat(w.long).toFixed(4)}`;
        if (statTotalTaken) statTotalTaken.innerText = totalTakenItems.toLocaleString("id-ID") + " Unit";
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

        allTakenStocksData = takenStocks.sort((a, b) => {
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


@extends('layouts.admin')

@section('title', 'Laporan Analitik')

@section('content')
    <div class="admin-page-heading">
        <div>
            <span class="admin-eyebrow">ANALISIS & PERFORMA</span>
            <h1>Laporan Penjualan & Performa</h1>
            <p>Ringkasan data transaksi real-time sesuai database berdasarkan tahun.</p>
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
            <label for="report-year-select" style="font-size:12px; font-weight:600; color:#4a4f59;">Pilih Tahun:</label>
            <select id="report-year-select" class="admin-select" style="padding:6px 12px; font-size:13px; font-weight:600; border-radius:6px; background:#fff;">
                <option value="2026">2026</option>
                <option value="2025">2025</option>
                <option value="2024">2024</option>
            </select>
        </div>
    </div>

    <!-- Summary KPI Cards (Horizontal 4-Column Grid) -->
    <div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin-bottom: 24px;">
        <article class="admin-panel admin-stat-card" style="margin:0;">
            <span class="admin-stat-card__eyebrow">TOTAL PENDAPATAN</span>
            <div class="admin-stat-card__value" id="report-stat-sales" style="font-size:22px; font-weight:700; color:#111318; margin:6px 0 4px;">Rp 0</div>
            <p class="admin-stat-card__note" style="margin:0; font-size:11px; color:#6d727c;">Total pendapatan diterima</p>
        </article>

        <article class="admin-panel admin-stat-card" style="margin:0;">
            <span class="admin-stat-card__eyebrow">TOTAL TRANSAKSI</span>
            <div class="admin-stat-card__value" id="report-stat-orders" style="font-size:22px; font-weight:700; color:#111318; margin:6px 0 4px;">0</div>
            <p class="admin-stat-card__note" id="report-stat-completed-note" style="margin:0; font-size:11px; color:#6d727c;">0 Selesai</p>
        </article>

        <article class="admin-panel admin-stat-card" style="margin:0;">
            <span class="admin-stat-card__eyebrow">RATA-RATA TRANSAKSI</span>
            <div class="admin-stat-card__value" id="report-stat-avg" style="font-size:22px; font-weight:700; color:#111318; margin:6px 0 4px;">Rp 0</div>
            <p class="admin-stat-card__note" style="margin:0; font-size:11px; color:#6d727c;">Nilai rata-rata per order</p>
        </article>

        <article class="admin-panel admin-stat-card" style="margin:0;">
            <span class="admin-stat-card__eyebrow">PESANAN DIBATALKAN</span>
            <div class="admin-stat-card__value" id="report-stat-cancelled" style="font-size:22px; font-weight:700; color:#ba1b2b; margin:6px 0 4px;">0 (0%)</div>
            <p class="admin-stat-card__note" style="margin:0; font-size:11px; color:#6d727c;">Tingkat pembatalan order</p>
        </article>
    </div>

    <!-- Monthly Revenue Chart -->
    <article class="admin-panel admin-chart-panel" style="margin-bottom: 24px;">
        <div class="admin-panel__head" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <div>
                <span class="admin-panel__eyebrow">GRAFIK REVENUE</span>
                <h2 id="chart-title">Pendapatan Bulanan Tahun 2026</h2>
            </div>
            <span id="chart-max-label" style="font-size:12px; color:#6d727c; font-weight:500;"></span>
        </div>
        
        <div id="chart-bars-container" style="height: 250px; display: flex; align-items: flex-end; justify-content: space-between; gap: 8px; padding: 20px 10px 10px; border-bottom: 1px solid #e5e7eb; position: relative;">
            <div style="position:absolute; inset:0; display:flex; flex-direction:column; justify-content:space-between; pointer-events:none; opacity:0.15; z-index:0;">
                <div style="border-top:1px dashed #000; width:100%;"></div>
                <div style="border-top:1px dashed #000; width:100%;"></div>
                <div style="border-top:1px dashed #000; width:100%;"></div>
            </div>
            <!-- Dynamic bars will be rendered here by admin-api.js -->
        </div>
        <div id="chart-labels-container" style="display: flex; justify-content: space-between; gap: 8px; padding: 8px 10px 0;">
            <!-- Month labels will be rendered here -->
        </div>
    </article>

    <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px;">
        <article class="admin-panel" style="margin:0;">
            <div class="admin-panel__head">
                <div>
                    <span class="admin-panel__eyebrow">PRODUK TERLARIS</span>
                    <h2>Top 5 Jenis Aki Sold</h2>
                </div>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table admin-table--compact">
                    <thead>
                        <tr>
                            <th>Brand</th>
                            <th>Tipe Aki</th>
                            <th style="text-align:right;">Jumlah Terjual</th>
                        </tr>
                    </thead>
                    <tbody id="top-accus-tbody">
                        <tr><td colspan="3"><div class="admin-table-empty">Memuat data...</div></td></tr>
                    </tbody>
                </table>
            </div>
        </article>

        <article class="admin-panel" style="margin:0;">
            <div class="admin-panel__head">
                <div>
                    <span class="admin-panel__eyebrow">AREA TERBANYAK</span>
                    <h2>Top 5 Kota Layanan</h2>
                </div>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table admin-table--compact">
                    <thead>
                        <tr>
                            <th>Kota</th>
                            <th style="text-align:right;">Total Pesanan</th>
                        </tr>
                    </thead>
                    <tbody id="top-cities-tbody">
                        <tr><td colspan="2"><div class="admin-table-empty">Memuat data...</div></td></tr>
                    </tbody>
                </table>
            </div>
        </article>
    </div>
@endsection

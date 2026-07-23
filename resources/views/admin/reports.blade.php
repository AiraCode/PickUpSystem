@extends('layouts.admin')

@section('title', 'Laporan')

@section('content')
<div class="admin-page-heading">
    <div>
        <span class="admin-eyebrow">ANALISIS</span>
        <h1>Laporan</h1>
        <p>Ringkasan data analitik untuk evaluasi bisnis.</p>
    </div>
    {{-- <div class="admin-page-heading__actions">
        <button type="button" class="admin-button admin-button--primary">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 4.5h14v15H5zM8 8h8M8 12h8M8 16h5" /></svg>
            Export PDF
        </button>
    </div> --}}
</div>

<div class="admin-dashboard-grid admin-dashboard-grid--main">
    <article class="admin-panel admin-chart-panel">
        <div class="admin-panel__head">
            <div>
                <span class="admin-panel__eyebrow">PENDAPATAN</span>
                <h2>Grafik Pendapatan Bulanan</h2>
            </div>
            <select class="admin-select" aria-label="Tahun">
                <option>2026</option>
            </select>
        </div>
        <div id="chart-container" style="height: 250px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #a0a3ab; border: 1px dashed #e0e2e5; border-radius: 8px; gap: 10px;">
            <svg viewBox="0 0 24 24" style="width: 40px; height: 40px; fill: none; stroke: currentColor; stroke-width: 1.5; stroke-linecap: round; stroke-linejoin: round;"><path d="M4 18V6M4 18h16" /><path d="m7 14 3-3 3 2 5-6" /></svg>
            <span>[Area Data Laporan - Segera Hadir]</span>
        </div>
    </article>

    <article class="admin-panel admin-activity-panel">
        <div class="admin-panel__head">
            <div>
                <span class="admin-panel__eyebrow">RINGKASAN</span>
                <h2>Statistik Kinerja</h2>
            </div>
        </div>
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <div style="display: flex; justify-content: space-between; padding: 12px; border: 1px solid #e5e7eb; border-radius: 8px;">
                <span style="color: #6d727c; font-size: 13px;">Total Pengguna Aktif</span>
                <strong style="color: #111318; font-size: 14px;">1,240</strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 12px; border: 1px solid #e5e7eb; border-radius: 8px;">
                <span style="color: #6d727c; font-size: 13px;">Rata-rata Penilaian</span>
                <strong style="color: #111318; font-size: 14px;">4.8 / 5.0</strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 12px; border: 1px solid #e5e7eb; border-radius: 8px;">
                <span style="color: #6d727c; font-size: 13px;">Pesanan Dibatalkan</span>
                <strong style="color: #ba1b2b; font-size: 14px;">12 (0.9%)</strong>
            </div>
        </div>
    </article>
</div>
@endsection

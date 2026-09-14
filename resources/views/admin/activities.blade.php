@extends('layouts.admin')

@section('title', 'Semua Aktivitas')

@section('content')
    <div class="admin-page-heading">
        <div>
            <span class="admin-eyebrow">OPERASIONAL &amp; MANAJEMEN</span>
            <h1>Semua Aktivitas</h1>
            <p>Pantau seluruh aktivitas dan perubahan status di dalam sistem.</p>
        </div>
    </div>

    <article class="admin-panel admin-table-panel">
        <div class="admin-panel__head" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <h2>Log Aktivitas</h2>
            <div style="display:flex; align-items:center; gap:8px;">
                <button type="button" id="btn-activities-clear-all" onclick="confirmClearAllActivities(event)" class="btn-clear-all" style="height:32px; padding:0 12px; display:none;">
                    <svg viewBox="0 0 24 24" style="width:13px; height:13px; fill:none; stroke:currentColor; stroke-width:2;"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    Hapus Semua
                </button>
                <button onclick="fetchActivitiesPage()" class="admin-button admin-button--secondary" style="height:32px; font-size:12px; padding:0 12px; display:inline-flex; align-items:center; gap:6px;">
                    <svg viewBox="0 0 24 24" style="width:14px; height:14px; fill:none; stroke:currentColor; stroke-width:2;">
                        <path d="M21 2v6h-6"></path>
                        <path d="M3 12a9 9 0 0 1 15-6.7L21 8"></path>
                        <path d="M3 22v-6h6"></path>
                        <path d="M21 12a9 9 0 0 1-15 6.7L3 16"></path>
                    </svg>
                    Refresh
                </button>
            </div>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:140px;">Waktu</th>
                        <th style="width:150px;">Tipe</th>
                        <th style="width:230px;">Judul</th>
                        <th>Deskripsi</th>
                        <th style="width:60px; text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="activities-tbody">
                    <tr>
                        <td colspan="5">
                            <div class="admin-table-empty">
                                <strong>Memuat data...</strong>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </article>
@endsection


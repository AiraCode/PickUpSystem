@extends('layouts.admin')

@section('title', 'Semua Aktivitas')

@section('content')
    <div class="admin-page-heading">
        <div>
            <span class="admin-eyebrow">Operasional &amp; Manajemen</span>
            <h1>Semua Aktivitas</h1>
            <p>Pantau seluruh aktivitas dan perubahan status di dalam sistem.</p>
        </div>
    </div>

    <article class="admin-panel admin-table-panel">
        <div class="admin-panel__head" style="display:flex; justify-content:space-between; align-items:center;">
            <h2>Log Aktivitas</h2>
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

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:140px;">Waktu</th>
                        <th style="width:140px;">Tipe</th>
                        <th style="width:220px;">Judul</th>
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            fetchActivitiesPage();
        });

        window.fetchActivitiesPage = function fetchActivitiesPage() {
            const tbody = document.getElementById('activities-tbody');
            tbody.innerHTML = '<tr><td colspan="5"><div class="admin-table-empty"><strong>Memuat data...</strong></div></td></tr>';
            
            fetch('/api/admin/activities', {
                headers: { 'Authorization': 'Bearer ' + localStorage.getItem('admin_token') }
            })
            .then(res => res.json())
            .then(res => {
                if(res.data && res.data.length > 0) {
                    tbody.innerHTML = res.data.map(a => {
                        const date = new Date(a.created_at);
                        const dateStr = date.toLocaleDateString('id-ID') + ' ' + date.toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});
                        
                        let typeBadge = '';
                        if(a.type === 'order_created') typeBadge = '<span style="background:#dbeafe; color:#1e40af; padding:2px 8px; border-radius:12px; font-size:11px; font-weight:600;">Pesanan Baru</span>';
                        else if(a.type === 'order_status_updated') typeBadge = '<span style="background:#fef3c7; color:#92400e; padding:2px 8px; border-radius:12px; font-size:11px; font-weight:600;">Update Status</span>';
                        else if(a.type === 'order_items_updated') typeBadge = '<span style="background:#e0e7ff; color:#3730a3; padding:2px 8px; border-radius:12px; font-size:11px; font-weight:600;">Edit Item</span>';
                        else if(a.type === 'order_edit_accepted') typeBadge = '<span style="background:#dcfce7; color:#166534; padding:2px 8px; border-radius:12px; font-size:11px; font-weight:600;">Edit Diterima</span>';
                        else if(a.type === 'order_edit_rejected') typeBadge = '<span style="background:#fee2e2; color:#991b1b; padding:2px 8px; border-radius:12px; font-size:11px; font-weight:600;">Edit Ditolak</span>';
                        else typeBadge = `<span style="background:#f3f4f6; color:#374151; padding:2px 8px; border-radius:12px; font-size:11px; font-weight:600;">${a.type}</span>`;
                        
                        return `
                        <tr onclick="openActivityOrder(${a.related_id || 0})" style="cursor:pointer;">
                            <td style="font-size:12px; color:#6b7280;">${dateStr}</td>
                            <td>${typeBadge}</td>
                            <td style="font-weight:600; color:#2563eb;">${a.title}</td>
                            <td style="color:#4b5563;">${a.description}</td>
                            <td style="text-align:center;" onclick="event.stopPropagation();">
                                <button type="button" onclick="dismissActivityNotification(event, ${a.id})" title="Hapus Notifikasi" style="border:none; background:transparent; color:#ef4444; font-size:16px; font-weight:bold; cursor:pointer; padding:2px 8px; border-radius:4px;">
                                    &times;
                                </button>
                            </td>
                        </tr>
                        `;
                    }).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="5"><div class="admin-table-empty"><strong>Belum ada aktivitas.</strong></div></td></tr>';
                }
            })
            .catch(err => {
                tbody.innerHTML = '<tr><td colspan="5"><div class="admin-table-empty"><strong style="color:red;">Gagal memuat data.</strong></div></td></tr>';
                console.error(err);
            });
        }
    </script>
@endsection

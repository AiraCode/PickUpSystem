@extends('layouts.admin')

@section('title', 'Audit Log Order')

@section('content')
    <div class="admin-page-heading">
        <div>
            <span class="admin-eyebrow">CENTRAL ONLY &mdash; AUDIT &amp; KEAMANAN</span>
            <h1>Audit Log Order</h1>
            <p>Riwayat lengkap seluruh perubahan pesanan: status, item, dan modifikasi lainnya.</p>
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
            <span id="audit-total-badge" style="
                background: linear-gradient(135deg,#6366f1,#4f46e5);
                color:#fff; padding:5px 14px; border-radius:20px;
                font-size:12px; font-weight:700; display:none;
            "></span>
        </div>
    </div>

    {{-- ACCESS DENIED STATE --}}
    <div id="audit-access-denied" style="display:none;">
        <article class="admin-panel" style="text-align:center; padding:64px 32px;">
            <div style="font-size:56px; margin-bottom:16px;">&#x1F512;</div>
            <h2 style="font-size:20px; font-weight:700; color:#ba1b2b; margin-bottom:8px;">Akses Ditolak</h2>
            <p style="font-size:14px; color:#6b7280; max-width:400px; margin:0 auto 24px;">
                Halaman ini hanya dapat diakses oleh admin dengan role <strong>Central</strong>.
                Silakan hubungi administrator sistem jika Anda membutuhkan akses.
            </p>
            <a href="/admin/dashboard" class="admin-button admin-button--primary"
               style="display:inline-flex; align-items:center; gap:8px; text-decoration:none;">
                <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2;">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                Kembali ke Dashboard
            </a>
        </article>
    </div>

    {{-- MAIN CONTENT --}}
    <div id="audit-main-content" style="display:none;">

        {{-- Filter Bar --}}
        <article class="admin-panel" style="margin-bottom:20px; padding:16px 20px;">
            <div style="display:flex; flex-wrap:wrap; align-items:center; gap:12px;">
                <div style="flex:1; min-width:200px; position:relative;">
                    <svg viewBox="0 0 24 24" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:15px;height:15px;fill:none;stroke:#9ca3af;stroke-width:2;pointer-events:none;">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="text" id="audit-search" placeholder="Cari deskripsi atau Order ID..."
                        style="width:100%;padding:8px 10px 8px 34px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;box-sizing:border-box;background:inherit;color:inherit;"
                        oninput="debounceAuditFetch()">
                </div>

                <button type="button" onclick="fetchAuditLog(1)"
                    class="admin-button admin-button--secondary"
                    style="height:36px;font-size:12px;padding:0 14px;display:inline-flex;align-items:center;gap:6px;">
                    <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2;">
                        <path d="M21 2v6h-6"/><path d="M3 12a9 9 0 0 1 15-6.7L21 8"/>
                        <path d="M3 22v-6h6"/><path d="M21 12a9 9 0 0 1-15 6.7L3 16"/>
                    </svg>
                    Refresh
                </button>
            </div>
        </article>

        {{-- Data Table --}}
        <article class="admin-panel admin-table-panel">
            <div class="admin-panel__head"
                style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
                <div>
                    <span class="admin-panel__eyebrow">RIWAYAT PERUBAHAN PESANAN</span>
                    <h2>Audit Log Order</h2>
                </div>
                <span id="audit-page-info" style="font-size:12px;color:#6b7280;"></span>
            </div>

            <div class="admin-table-wrap">
                <table class="admin-table" id="audit-table">
                    <thead>
                        <tr>
                            <th style="width:150px;">Timestamp</th>
                            <th style="width:130px;">Order ID</th>
                            <th style="width:140px;">Pengubah</th>
                            <th style="width:100px;">Actor</th>
                            <th style="width:130px;">Tipe Aksi</th>
                            <th style="width:180px;">Nilai Lama</th>
                            <th style="width:180px;">Nilai Baru</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody id="audit-tbody">
                        <tr>
                            <td colspan="8">
                                <div class="admin-table-empty">
                                    <strong>Memuat data...</strong>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div id="audit-pagination"
                style="display:flex;justify-content:space-between;align-items:center;padding:14px 20px;border-top:1px solid #f0f0f0;flex-wrap:wrap;gap:10px;">
                <span id="audit-pagination-summary" style="font-size:12px;color:#6b7280;"></span>
                <div id="audit-pagination-btns" style="display:flex;gap:6px;flex-wrap:wrap;"></div>
            </div>
        </article>
    </div>

    {{-- JSON Detail Modal --}}
    <div id="audit-json-modal"
        style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:5000;align-items:center;justify-content:center;">
        <div style="background:#1e1e2e;color:#cdd6f4;border-radius:14px;width:720px;max-width:94vw;max-height:85vh;display:flex;flex-direction:column;box-shadow:0 24px 64px rgba(0,0,0,.5);overflow:hidden;">
            <div style="display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid #313244;">
                <h3 id="audit-json-modal-title"
                    style="font-size:14px;font-weight:700;color:#cba6f7;margin:0;">Detail Nilai</h3>
                <button type="button" id="audit-json-modal-close"
                    style="background:#313244;border:none;color:#cdd6f4;width:28px;height:28px;border-radius:6px;cursor:pointer;font-size:18px;display:flex;align-items:center;justify-content:center;line-height:1;">
                    &times;
                </button>
            </div>
            <div style="overflow-y:auto;padding:20px;">
                <pre id="audit-json-modal-content"
                    style="margin:0;font-family:'Fira Code','Courier New',monospace;font-size:13px;line-height:1.7;white-space:pre-wrap;word-break:break-word;color:#a6e3a1;"></pre>
            </div>
        </div>
    </div>
@endsection

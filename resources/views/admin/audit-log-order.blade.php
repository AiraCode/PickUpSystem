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

    <script>
    (function () {
        'use strict';

        // ── Role Guard ────────────────────────────────────────────────────────
        var adminUser = null;
        try { adminUser = JSON.parse(localStorage.getItem('admin_user') || 'null'); } catch(e) {}
        var userRole = adminUser ? adminUser.role : null;

        var mainEl = document.getElementById('audit-main-content');
        var denyEl = document.getElementById('audit-access-denied');

        if (userRole !== 'central') {
            if (denyEl) denyEl.style.display = 'block';
            if (mainEl) mainEl.style.display = 'none';
            return;
        }
        if (mainEl) mainEl.style.display = 'block';
        if (denyEl) denyEl.style.display = 'none';

        // ── Constants ─────────────────────────────────────────────────────────
        var API_BASE     = '/api/admin/order-histories';
        var token        = localStorage.getItem('admin_token');
        var currentPage  = 1;
        var debounceTimer = null;

        function authHeaders() {
            var h = { 'Accept': 'application/json' };
            if (token) h['Authorization'] = 'Bearer ' + token;
            return h;
        }

        // ── Helpers ───────────────────────────────────────────────────────────
        function escHtml(str) {
            return String(str || '')
                .replace(/&/g,'&amp;').replace(/</g,'&lt;')
                .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        function fmtDate(val) {
            if (!val) return '—';
            var s = String(val);
            if (!s.includes('T') && s.includes(' ')) s = s.replace(' ', 'T');
            if (!s.includes('Z') && !s.includes('+')) s += 'Z';
            var d = new Date(s);
            return d.toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'})
                 + '\n' + d.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit', second:'2-digit'});
        }

        var ACTION_BADGES = {
            status_change: { bg:'#fef3c7', c:'#92400e', lbl:'Status Change' },
            items_change:  { bg:'#e0e7ff', c:'#3730a3', lbl:'Items Change'  },
            note_update:   { bg:'#ccfbf1', c:'#0f766e', lbl:'Note Update'   },
            created:       { bg:'#dbeafe', c:'#1e40af', lbl:'Created'       },
            cancelled:     { bg:'#fee2e2', c:'#991b1b', lbl:'Cancelled'     },
        };

        function actionBadge(type) {
            var cfg = ACTION_BADGES[type];
            if (cfg) return '<span style="background:' + cfg.bg + ';color:' + cfg.c + ';padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;white-space:nowrap;">' + cfg.lbl + '</span>';
            return '<span style="background:#f3f4f6;color:#374151;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;white-space:nowrap;">' + escHtml(type || '—') + '</span>';
        }

        var ACTOR_BADGES = {
            admin:    { bg:'#ede9fe', c:'#5b21b6' },
            customer: { bg:'#dcfce7', c:'#166534' },
            system:   { bg:'#f1f5f9', c:'#475569' },
        };

        function actorBadge(type) {
            var cfg = ACTOR_BADGES[type];
            if (cfg) return '<span style="background:' + cfg.bg + ';color:' + cfg.c + ';padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;">' + escHtml(type) + '</span>';
            return '<span style="background:#f3f4f6;color:#374151;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;">' + escHtml(type || '—') + '</span>';
        }

        function jsonPreview(obj) {
            if (!obj) return '<span style="color:#9ca3af;font-size:11px;">—</span>';
            if (typeof obj === 'object' && Object.keys(obj).length === 0)
                return '<span style="color:#9ca3af;font-size:11px;">—</span>';
            var str = typeof obj === 'string' ? obj : JSON.stringify(obj);
            var preview = str.length > 70 ? str.substring(0, 68) + '…' : str;
            return '<span style="font-family:\'Courier New\',monospace;font-size:10px;color:#6366f1;cursor:pointer;line-height:1.5;display:block;">' + escHtml(preview) + '</span>';
        }

        // ── Main Fetch ────────────────────────────────────────────────────────
        window.fetchAuditLog = function(page) {
            page = page || 1;
            currentPage = page;

            var tbody   = document.getElementById('audit-tbody');
            var search  = (document.getElementById('audit-search')     || {}).value || '';
            var actType = (document.getElementById('audit-filter-action') || {}).value || '';
            var actorT  = (document.getElementById('audit-filter-actor')  || {}).value || '';

            if (tbody) {
                tbody.innerHTML = '<tr><td colspan="8"><div class="admin-table-empty"><strong>Memuat data...</strong></div></td></tr>';
            }

            var params = 'page=' + page;
            if (search.trim()) params += '&search=' + encodeURIComponent(search.trim());
            if (actType)       params += '&action_type=' + encodeURIComponent(actType);
            if (actorT)        params += '&actor_type='  + encodeURIComponent(actorT);

            fetch(API_BASE + '?' + params, { headers: authHeaders() })
                .then(function(res) {
                    if (res.status === 403) throw new Error('403');
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    return res.json();
                })
                .then(function(res) {
                    var raw   = res.data || {};
                    var items = raw.data || [];
                    var pg = {
                        current_page: raw.current_page || 1,
                        last_page:    raw.last_page    || 1,
                        per_page:     raw.per_page     || 15,
                        total:        raw.total        || 0,
                        from:         raw.from,
                        to:           raw.to,
                    };

                    renderTable(items);
                    renderPagination(pg);

                    var badge = document.getElementById('audit-total-badge');
                    if (badge) { badge.textContent = pg.total + ' Record'; badge.style.display = 'inline-block'; }

                    var info = document.getElementById('audit-page-info');
                    if (info) {
                        info.textContent = pg.total > 0
                            ? 'Menampilkan ' + (pg.from||0) + '–' + (pg.to||0) + ' dari ' + pg.total + ' record'
                            : '';
                    }
                })
                .catch(function(err) {
                    if (tbody) {
                        var msg = err.message === '403'
                            ? 'Akses ditolak. Hanya role Central.'
                            : 'Gagal memuat data: ' + err.message;
                        tbody.innerHTML = '<tr><td colspan="8"><div class="admin-table-empty"><strong style="color:#ba1b2b;">' + escHtml(msg) + '</strong></div></td></tr>';
                    }
                    console.error(err);
                });
        };

        // ── Render Table ──────────────────────────────────────────────────────
        function renderTable(items) {
            var tbody = document.getElementById('audit-tbody');
            if (!tbody) return;

            if (!items.length) {
                tbody.innerHTML = '<tr><td colspan="8"><div class="admin-table-empty"><strong>Tidak ada data riwayat pesanan.</strong></div></td></tr>';
                return;
            }

            tbody.innerHTML = items.map(function(item) {
                var dtParts = fmtDate(item.created_at).split('\n');
                var dtDate  = dtParts[0] || '';
                var dtTime  = dtParts[1] || '';

                var orderLabel = item.order_uuid
                    ? '<span style="color:#2563eb;font-weight:700;">#' + escHtml(item.order_uuid.substring(0,8).toUpperCase()) + '</span><br><span style="font-size:10px;color:#9ca3af;">' + escHtml(item.order_uuid) + '</span>'
                    : '<span style="color:#6b7280;">#' + escHtml(String(item.order_id)) + '</span>';

                var pengubah = item.user_name
                    ? '<span style="font-weight:600;">' + escHtml(item.user_name) + '</span><br><span style="font-size:10px;color:#9ca3af;">ID: ' + escHtml(String(item.user_id || '—')) + '</span>'
                    : '<span style="color:#9ca3af;font-size:11px;">System</span>';

                var oldHas = item.old_values && (typeof item.old_values === 'object' ? Object.keys(item.old_values).length > 0 : true);
                var newHas = item.new_values && (typeof item.new_values === 'object' ? Object.keys(item.new_values).length > 0 : true);

                var oldJsonStr = oldHas ? JSON.stringify(item.old_values, null, 2) : null;
                var newJsonStr = newHas ? JSON.stringify(item.new_values, null, 2) : null;

                var oldCell = oldHas
                    ? '<div onclick="openAuditJsonModal(\'Nilai Lama\', ' + escHtml(JSON.stringify(oldJsonStr)) + ')" title="Klik untuk detail" style="cursor:pointer;">' + jsonPreview(item.old_values) + '</div>'
                    : '<span style="color:#9ca3af;font-size:11px;">—</span>';

                var newCell = newHas
                    ? '<div onclick="openAuditJsonModal(\'Nilai Baru\', ' + escHtml(JSON.stringify(newJsonStr)) + ')" title="Klik untuk detail" style="cursor:pointer;">' + jsonPreview(item.new_values) + '</div>'
                    : '<span style="color:#9ca3af;font-size:11px;">—</span>';

                return '<tr>'
                    + '<td style="font-size:11px;white-space:nowrap;"><span style="display:block;font-weight:600;">' + escHtml(dtDate) + '</span><span style="color:#9ca3af;">' + escHtml(dtTime) + '</span></td>'
                    + '<td>' + orderLabel + '</td>'
                    + '<td>' + pengubah + '</td>'
                    + '<td>' + actorBadge(item.actor_type) + '</td>'
                    + '<td>' + actionBadge(item.action_type) + '</td>'
                    + '<td style="max-width:180px;overflow:hidden;">' + oldCell + '</td>'
                    + '<td style="max-width:180px;overflow:hidden;">' + newCell + '</td>'
                    + '<td style="font-size:12px;color:#9ca3af;line-height:1.5;">' + (item.description ? escHtml(item.description) : '<span style="color:#9ca3af;">—</span>') + '</td>'
                    + '</tr>';
            }).join('');
        }

        // ── Render Pagination ─────────────────────────────────────────────────
        function renderPagination(pg) {
            var wrap    = document.getElementById('audit-pagination');
            var summary = document.getElementById('audit-pagination-summary');
            var btns    = document.getElementById('audit-pagination-btns');

            if (!pg || pg.total === 0) {
                if (wrap) wrap.style.display = 'none';
                return;
            }
            if (wrap) wrap.style.display = 'flex';
            if (summary) summary.textContent = 'Halaman ' + pg.current_page + ' dari ' + pg.last_page + ' (Total: ' + pg.total + ')';
            if (!btns) return;
            btns.innerHTML = '';

            function makeBtn(label, page, isActive, disabled) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.innerHTML = label;
                btn.disabled = disabled || isActive;
                btn.style.cssText = [
                    'height:32px;min-width:32px;padding:0 10px;',
                    'border:1px solid ' + (isActive ? '#6366f1' : '#e5e7eb') + ';',
                    'background:' + (isActive ? '#6366f1' : 'transparent') + ';',
                    'color:' + (isActive ? '#fff' : '#374151') + ';',
                    'border-radius:6px;cursor:' + (disabled || isActive ? 'default' : 'pointer') + ';',
                    'font-size:12px;font-weight:600;',
                    'display:inline-flex;align-items:center;justify-content:center;',
                    'transition:background 0.15s;',
                    (disabled ? 'opacity:0.35;' : ''),
                ].join('');
                if (!isActive && !disabled) {
                    btn.addEventListener('mouseover', function() { btn.style.background = '#f3f4f6'; });
                    btn.addEventListener('mouseout',  function() { btn.style.background = 'transparent'; });
                    btn.addEventListener('click',     function() { fetchAuditLog(page); });
                }
                return btn;
            }

            btns.appendChild(makeBtn('&lsaquo;', pg.current_page - 1, false, pg.current_page <= 1));

            var start = Math.max(1, pg.current_page - 2);
            var end   = Math.min(pg.last_page, pg.current_page + 2);

            if (start > 1) {
                btns.appendChild(makeBtn('1', 1, false, false));
                if (start > 2) {
                    var e1 = document.createElement('span');
                    e1.textContent = '…';
                    e1.style.cssText = 'padding:0 4px;color:#9ca3af;font-size:12px;align-self:center;';
                    btns.appendChild(e1);
                }
            }

            for (var i = start; i <= end; i++) {
                btns.appendChild(makeBtn(i, i, i === pg.current_page, false));
            }

            if (end < pg.last_page) {
                if (end < pg.last_page - 1) {
                    var e2 = document.createElement('span');
                    e2.textContent = '…';
                    e2.style.cssText = 'padding:0 4px;color:#9ca3af;font-size:12px;align-self:center;';
                    btns.appendChild(e2);
                }
                btns.appendChild(makeBtn(pg.last_page, pg.last_page, false, false));
            }

            btns.appendChild(makeBtn('&rsaquo;', pg.current_page + 1, false, pg.current_page >= pg.last_page));
        }

        // ── JSON Modal ────────────────────────────────────────────────────────
        window.openAuditJsonModal = function(title, jsonStr) {
            var modal   = document.getElementById('audit-json-modal');
            var titleEl = document.getElementById('audit-json-modal-title');
            var content = document.getElementById('audit-json-modal-content');
            if (!modal) return;
            try {
                var parsed = JSON.parse(jsonStr);
                content.textContent = JSON.stringify(parsed, null, 2);
            } catch(e) {
                content.textContent = jsonStr;
            }
            if (titleEl) titleEl.textContent = title;
            modal.style.display = 'flex';
        };

        window.closeAuditJsonModal = function() {
            var modal = document.getElementById('audit-json-modal');
            if (modal) modal.style.display = 'none';
        };

        var closeBtn = document.getElementById('audit-json-modal-close');
        if (closeBtn) closeBtn.addEventListener('click', closeAuditJsonModal);

        var jsonModal = document.getElementById('audit-json-modal');
        if (jsonModal) {
            jsonModal.addEventListener('click', function(e) {
                if (e.target === jsonModal) closeAuditJsonModal();
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeAuditJsonModal();
        });

        // ── Debounce Search ───────────────────────────────────────────────────
        window.debounceAuditFetch = function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function() { fetchAuditLog(1); }, 380);
        };

        // ── Initial Load ──────────────────────────────────────────────────────
        fetchAuditLog(1);

    })();
    </script>
@endsection

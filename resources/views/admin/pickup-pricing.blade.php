@extends('layouts.admin')

@section('title', 'Biaya Penjemputan')

@section('content')
    <div class="admin-page-heading">
        <div>
            <span class="admin-eyebrow">OPERASIONAL &amp; MANAJEMEN</span>
            <h1>Biaya Penjemputan</h1>
            <p>Kelola formula biaya, multiplier dinamis, dan riwayat perubahan pengaturan penjemputan.</p>
        </div>
    </div>

    {{-- ===================================================================
         CARD 1 : Pengaturan Rumus Biaya Penjemputan
    =================================================================== --}}
    <article class="admin-panel" style="margin-bottom:24px;">
        <div class="admin-panel__head">
            <div>
                <h2>Pengaturan Rumus Biaya Penjemputan</h2>
                <p style="font-size:12px; color:#6d727c; margin-top:2px;">
                    Rumus: <strong>Biaya Awal + (Jarak × Tarif/km) + (Waktu × Tarif/detik)</strong>
                </p>
            </div>
        </div>

        <form id="form-pricing-formula" style="padding-top:8px;">
            <div style="display:flex; flex-wrap:wrap; gap:16px; align-items:flex-end; margin-bottom:20px;">
                <div style="flex:1; min-width:180px;">
                    <label class="pp-label">Biaya Awal (Rp)</label>
                    <input type="number" step="1" min="0" id="pp-initial-fee" class="admin-select"
                        style="width:100%; padding:8px 12px; border-radius:6px;"
                        placeholder="5000" required>
                </div>
                <div style="flex:1; min-width:180px;">
                    <label class="pp-label">Tarif per KM (Rp)</label>
                    <input type="number" step="0.01" min="0.01" id="pp-distance-rate" class="admin-select"
                        style="width:100%; padding:8px 12px; border-radius:6px;"
                        placeholder="2300" required>
                </div>
                <div style="flex:1; min-width:180px;">
                    <label class="pp-label">Tarif per Detik (Rp)</label>
                    <input type="number" step="0.01" min="0.01" id="pp-time-rate" class="admin-select"
                        style="width:100%; padding:8px 12px; border-radius:6px;"
                        placeholder="25" required>
                </div>
            </div>

            {{-- Formula Preview (read-only) --}}
            <div style="background:#f1f5f9; border-radius:10px; padding:16px 20px; margin-bottom:20px; border:1px solid #e2e8f0;">
                <p style="font-size:11px; font-weight:700; color:#64748b; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.5px;">
                    Preview Rumus (Read-only)</p>
                <div style="display:flex; align-items:center; flex-wrap:wrap; gap:8px; font-size:14px; font-weight:600; color:#1e293b;">
                    <span style="background:#fff; border:1px solid #cbd5e1; border-radius:6px; padding:6px 14px;" id="prev-initial-fee">Rp5.000</span>
                    <span style="color:#64748b;">+</span>
                    <span style="color:#64748b;">(</span>
                    <span style="background:#fff; border:1px solid #cbd5e1; border-radius:6px; padding:6px 14px;">Jarak</span>
                    <span style="color:#64748b;">×</span>
                    <span style="background:#fff; border:1px solid #cbd5e1; border-radius:6px; padding:6px 14px;" id="prev-distance-rate">Rp2.300/km</span>
                    <span style="color:#64748b;">)</span>
                    <span style="color:#64748b;">+</span>
                    <span style="color:#64748b;">(</span>
                    <span style="background:#fff; border:1px solid #cbd5e1; border-radius:6px; padding:6px 14px;">Waktu</span>
                    <span style="color:#64748b;">×</span>
                    <span style="background:#fff; border:1px solid #cbd5e1; border-radius:6px; padding:6px 14px;" id="prev-time-rate">Rp25/detik</span>
                    <span style="color:#64748b;">)</span>
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; flex-wrap:wrap;">
                <button type="submit" class="admin-button admin-button--primary" id="btn-save-formula"
                    style="min-width:180px;">
                    Simpan Pengaturan Formula
                </button>
            </div>
        </form>
    </article>

    {{-- ===================================================================
         CARD 2 : Pengaturan Multiplier Dynamic Pricing
    =================================================================== --}}
    <article class="admin-panel" style="margin-bottom:24px;">
        <div class="admin-panel__head">
            <div>
                <h2>Pengaturan Multiplier Dynamic Pricing</h2>
                <p style="font-size:12px; color:#6d727c; margin-top:2px;">
                    Nilai minimum 0.10 &mdash; maksimum 5.00. Total multiplier ditampilkan secara real-time.
                </p>
            </div>
        </div>

        <form id="form-pricing-multiplier" style="padding-top:8px;">
            <div style="display:flex; flex-wrap:wrap; gap:16px; margin-bottom:20px;">
                <div style="flex:1; min-width:160px;">
                    <label class="pp-label">Demand</label>
                    <input type="number" step="0.01" min="0.10" max="5.00" id="pp-demand" class="admin-select"
                        style="width:100%; padding:8px 12px; border-radius:6px;"
                        placeholder="1.00" required>
                </div>
                <div style="flex:1; min-width:160px;">
                    <label class="pp-label">Weather</label>
                    <input type="number" step="0.01" min="0.10" max="5.00" id="pp-weather" class="admin-select"
                        style="width:100%; padding:8px 12px; border-radius:6px;"
                        placeholder="1.00" required>
                </div>
                <div style="flex:1; min-width:160px;">
                    <label class="pp-label">Traffic</label>
                    <input type="number" step="0.01" min="0.10" max="5.00" id="pp-traffic" class="admin-select"
                        style="width:100%; padding:8px 12px; border-radius:6px;"
                        placeholder="1.00" required>
                </div>
                <div style="flex:1; min-width:160px;">
                    <label class="pp-label">Event</label>
                    <input type="number" step="0.01" min="0.10" max="5.00" id="pp-event" class="admin-select"
                        style="width:100%; padding:8px 12px; border-radius:6px;"
                        placeholder="1.00" required>
                </div>
            </div>

            {{-- Live Total Multiplier Preview --}}
            <div style="background:#f1f5f9; border-radius:10px; padding:16px 20px; margin-bottom:20px; border:1px solid #e2e8f0;">
                <p style="font-size:11px; font-weight:700; color:#64748b; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.5px;">
                    Total Multiplier (Real-time)</p>
                <div style="display:flex; align-items:center; flex-wrap:wrap; gap:8px; font-size:14px; font-weight:600; color:#1e293b;">
                    <span style="background:#fff; border:1px solid #cbd5e1; border-radius:6px; padding:6px 14px;" id="live-demand">1.00</span>
                    <span style="color:#64748b;">×</span>
                    <span style="background:#fff; border:1px solid #cbd5e1; border-radius:6px; padding:6px 14px;" id="live-weather">1.00</span>
                    <span style="color:#64748b;">×</span>
                    <span style="background:#fff; border:1px solid #cbd5e1; border-radius:6px; padding:6px 14px;" id="live-traffic">1.00</span>
                    <span style="color:#64748b;">×</span>
                    <span style="background:#fff; border:1px solid #cbd5e1; border-radius:6px; padding:6px 14px;" id="live-event">1.00</span>
                    <span style="color:#64748b;">=</span>
                    <span id="live-total-multiplier"
                        style="background:#0369a1; color:#fff; border-radius:8px; padding:8px 20px; font-size:16px; font-weight:700;">1.0000</span>
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; flex-wrap:wrap;">
                <button type="submit" class="admin-button admin-button--primary" id="btn-save-multiplier"
                    style="min-width:180px;">
                    Simpan Multiplier
                </button>
            </div>
        </form>
    </article>

    {{-- ===================================================================
         CARD 3 : Riwayat Perubahan Pengaturan
    =================================================================== --}}
    <article class="admin-panel admin-table-panel">
        <div class="admin-panel__head">
            <div>
                <h2>Riwayat Perubahan Pengaturan</h2>
            </div>
            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <div style="position:relative; width:240px;">
                    <input type="text" id="pp-history-search" class="admin-select"
                        style="width:100%; padding:7px 32px 7px 12px; font-size:12px; border-radius:6px; background:#fff;"
                        placeholder="Cari nama admin...">
                    <span style="position:absolute; right:10px; top:50%; transform:translateY(-50%); color:#9ca3af; pointer-events:none;">
                        <svg viewBox="0 0 24 24" style="width:14px; height:14px; fill:none; stroke:currentColor; stroke-width:2;">
                            <circle cx="11" cy="11" r="8" /><line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                    </span>
                </div>
            </div>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table" id="pp-history-table">
                <thead>
                    <tr>
                        <th>Diubah Pada</th>
                        <th>Diubah Oleh</th>
                        <th style="text-align:right;">Biaya Awal</th>
                        <th style="text-align:right;">Tarif/km</th>
                        <th style="text-align:right;">Tarif/dtk</th>
                        <th style="text-align:right;">Demand</th>
                        <th style="text-align:right;">Weather</th>
                        <th style="text-align:right;">Traffic</th>
                        <th style="text-align:right;">Event</th>
                        <th style="text-align:right;">Total ×</th>
                    </tr>
                </thead>
                <tbody id="pp-history-tbody">
                    <tr>
                        <td colspan="10">
                            <div class="admin-table-empty"><strong>Memuat data...</strong></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div id="pp-history-pagination"
            style="display:flex; justify-content:center; gap:8px; padding:16px 20px; flex-wrap:wrap;"></div>
    </article>

    {{-- ===================================================================
         Toast notification (reuse admin pattern)
    =================================================================== --}}
    <div id="pp-toast"
        style="display:none; position:fixed; bottom:24px; right:24px; background:#16a34a; color:#fff;
               padding:12px 20px; border-radius:10px; font-size:13px; font-weight:600;
               box-shadow:0 4px 20px rgba(0,0,0,.15); z-index:999; max-width:320px;">
    </div>

    <style>
        .pp-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 6px;
            color: #4a4f59;
        }
        @media (max-width: 767px) {
            #pp-history-table th:nth-child(7),
            #pp-history-table td:nth-child(7),
            #pp-history-table th:nth-child(8),
            #pp-history-table td:nth-child(8),
            #pp-history-table th:nth-child(9),
            #pp-history-table td:nth-child(9) {
                display: none;
            }
        }
    </style>

    {{-- Easter Egg Modal Lock --}}
    <div id="modal-easter-egg-lock" style="display:none; position:fixed; inset:0; background:rgba(17,19,24,0.92); z-index:999; align-items:center; justify-content:center; backdrop-filter:blur(4px);">
        <div class="admin-panel" style="width:380px; text-align:center; padding:28px 24px; border:1px solid #374151; box-shadow:0 20px 25px -5px rgba(0,0,0,0.5);">
            <div style="width:54px; height:54px; background:#eff6ff; color:#2563eb; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                <svg viewBox="0 0 24 24" style="width:28px; height:28px; fill:none; stroke:currentColor; stroke-width:2; stroke-linecap:round; stroke-linejoin:round;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>
            <h2 style="font-size:18px; font-weight:700; color:#111318; margin-bottom:6px;">Akses Rahasia Pengiriman</h2>
            <p style="font-size:13px; color:#6d727c; margin-bottom:20px; line-height:1.4;">
                Halaman ini dilindungi kata sandi khusus. Masukkan kata sandi rahasia untuk membuka akses pengaturan pengiriman.
            </p>
            <form id="form-easter-egg-auth">
                <div style="margin-bottom:16px;">
                    <input type="password" id="easter-egg-password" class="admin-select" style="width:100%; padding:10px 12px; font-size:14px; text-align:center; border-radius:6px; letter-spacing:2px;" placeholder="Masukkan kata sandi..." required>
                    <div id="easter-egg-error" style="display:none; color:#ba1b2b; font-size:12px; margin-top:8px; font-weight:600;">Kata sandi rahasia salah!</div>
                </div>
                <div style="display:flex; gap:10px; justify-content:center;">
                    <a href="/admin/login" class="admin-button admin-button--secondary" style="text-decoration:none; display:inline-flex; align-items:center;">Ke Login</a>
                    <button type="submit" class="admin-button admin-button--primary">Buka Akses</button>
                </div>
            </form>
        </div>
    </div>
@endsection


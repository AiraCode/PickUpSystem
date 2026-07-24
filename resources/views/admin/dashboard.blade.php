@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="admin-page-heading">
        <div>
            <span class="admin-eyebrow">OVERVIEW</span>
            <h1>Dashboard</h1>
            <p>Kelola aktivitas Pick Up System dari satu ruang kerja terpusat.</p>
        </div>
    </div>

    <section class="admin-dashboard">

        <div class="admin-dashboard__left">

            {{-- 3 STAT CARDS PRECISELY ALIGNED WITH LEFT SECTION --}}
            <div style="display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:16px;">
                <article class="admin-stat-card">
                    <div class="admin-stat-card__head">
                        <span class="admin-stat-card__icon admin-stat-card__icon--blue">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16v12H4zM8 10h8M8 14h5" /></svg>
                        </span>
                    </div>
                    <p>Total transaksi</p>
                    <strong id="stat-total-transactions">0</strong>
                    <small>Dari awal sistem berjalan</small>
                </article>

                <article class="admin-stat-card">
                    <div class="admin-stat-card__head">
                        <span class="admin-stat-card__icon admin-stat-card__icon--red">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 4.5h12v15H6z" /><path d="M9 8.5h6M9 12h6M9 15.5h3" /></svg>
                        </span>
                    </div>
                    <p>Menunggu verifikasi</p>
                    <strong id="stat-pending-verifications">0</strong>
                    <small>Pesanan berstatus pending</small>
                </article>

                <article class="admin-stat-card">
                    <div class="admin-stat-card__head">
                        <span class="admin-stat-card__icon admin-stat-card__icon--blue">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4.5 7.5h15v11h-15z" /><path d="M8 7.5V5.8h8v1.7M8 12h8M8 15.5h4" /></svg>
                        </span>
                    </div>
                    <p>Nilai penjualan</p>
                    <strong id="stat-total-sales">Rp 0</strong>
                    <small>Total pendapatan kotor</small>
                </article>
            </div>

            {{-- PERFORMA --}}
            <article class="admin-panel admin-chart-panel">
                <div class="admin-panel__head">
                    <div>
                        <span class="admin-panel__eyebrow">PERFORMA</span>
                        <h2>Aktivitas transaksi</h2>
                    </div>

                    <select id="dashboard-period-select" class="admin-select" aria-label="Periode aktivitas transaksi">
                        <option value="7days">7 hari terakhir</option>
                        <option value="30days">30 hari terakhir</option>
                        <option value="year">Tahun ini</option>
                    </select>
                </div>

                <div class="admin-chart-empty" style="display: none;" id="chart-empty-state">
                    <div class="admin-empty-icon admin-empty-icon--blue">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M4 18V6M4 18h16" />
                            <path d="m7 14 3-3 3 2 5-6" />
                        </svg>
                    </div>
                    <strong>Belum ada aktivitas transaksi</strong>
                    <p>Data grafik akan tampil setelah transaksi pertama tercatat.</p>
                </div>

                <div id="chart-container" style="height:230px; display:flex; align-items:center; justify-content:center;">
                    [Memuat Grafik...]
                </div>
            </article>

            {{-- OPERASIONAL --}}
            <article class="admin-panel admin-table-panel">
                <div class="admin-panel__head">
                    <div>
                        <span class="admin-panel__eyebrow">OPERASIONAL</span>
                        <h2>Pesanan yang memerlukan perhatian</h2>
                    </div>

                    <a href="{{ url('/admin/transaksi') }}" class="admin-panel__link">
                        Kelola transaksi
                    </a>
                </div>

                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Nomor pesanan</th>
                                <th>Pelanggan</th>
                                <th>Kota</th>
                                <th>Waktu</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody id="attention-orders-tbody">
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

        </div>

        <aside class="admin-dashboard__right" style="flex-direction:column; gap:24px;">

            {{-- 4TH STAT CARD PRECISELY ALIGNED WITH RIGHT SECTION --}}
            <article class="admin-stat-card" style="margin:0;">
                <div class="admin-stat-card__head">
                    <span class="admin-stat-card__icon admin-stat-card__icon--red">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20a8 8 0 1 0 0-16 8 8 0 0 0 0 16Z" /><path d="M12 8v4l2.6 1.7" /></svg>
                    </span>
                </div>
                <p>Waktu proses rata-rata</p>
                <strong id="stat-avg-time">-</strong>
                <small>Dari request hingga selesai</small>
            </article>

            {{-- PEMBARUAN --}}
            <article class="admin-panel admin-activity-panel" style="flex:1;">
                <div class="admin-panel__head">
                    <div>
                        <span class="admin-panel__eyebrow">PEMBARUAN</span>
                        <h2>Aktivitas terbaru</h2>
                    </div>
                </div>

                <div class="admin-list-empty" id="activity-empty-state">
                    <div class="admin-empty-icon admin-empty-icon--red">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M5 5.5h14v13H5z" />
                            <path d="M8.5 9.5h7M8.5 13h4" />
                        </svg>
                    </div>

                    <strong>Memuat data...</strong>
                </div>

                <div id="activity-list-container" style="display:none;flex-direction:column;gap:12px;margin-top:15px;">
                </div>
            </article>

        </aside>

    </section>
@endsection

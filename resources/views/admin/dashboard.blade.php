@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="admin-page-heading">
        <div>
            <span class="admin-eyebrow">OVERVIEW</span>
            <h1>Dashboard</h1>
            <p>Kelola aktivitas Pick Up System dari satu ruang kerja terpusat.</p>
        </div>
        <div class="admin-page-heading__actions">
            <button type="button" class="admin-button admin-button--secondary">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M5 4.5h14v15H5zM8 8h8M8 12h8M8 16h5" />
                </svg>
                Export laporan
            </button>
            <button type="button" class="admin-button admin-button--primary">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 5v14M5 12h14" />
                </svg>
                Tambah data
            </button>
        </div>
    </div>

    <section class="admin-stat-grid" id="dashboard-stats-container">
        <article class="admin-stat-card">
            <div class="admin-stat-card__head">
                <span class="admin-stat-card__icon admin-stat-card__icon--blue"><svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 6h16v12H4zM8 10h8M8 14h5" />
                    </svg></span>
                <span class="admin-stat-card__trend"></span>
            </div>
            <p>Total transaksi</p>
            <strong id="stat-total-transactions">0</strong>
            <small>Dari awal sistem berjalan</small>
        </article>
        <article class="admin-stat-card">
            <div class="admin-stat-card__head">
                <span class="admin-stat-card__icon admin-stat-card__icon--red"><svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6 4.5h12v15H6z" />
                        <path d="M9 8.5h6M9 12h6M9 15.5h3" />
                    </svg></span>
                <span class="admin-stat-card__trend"></span>
            </div>
            <p>Menunggu verifikasi</p>
            <strong id="stat-pending-verifications">0</strong>
            <small>Pesanan berstatus pending</small>
        </article>
        <article class="admin-stat-card">
            <div class="admin-stat-card__head">
                <span class="admin-stat-card__icon admin-stat-card__icon--blue"><svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4.5 7.5h15v11h-15z" />
                        <path d="M8 7.5V5.8h8v1.7M8 12h8M8 15.5h4" />
                    </svg></span>
                <span class="admin-stat-card__trend"></span>
            </div>
            <p>Nilai penjualan</p>
            <strong id="stat-total-sales">Rp 0</strong>
            <small>Total pendapatan kotor</small>
        </article>
        <article class="admin-stat-card">
            <div class="admin-stat-card__head">
                <span class="admin-stat-card__icon admin-stat-card__icon--red"><svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 20a8 8 0 1 0 0-16 8 8 0 0 0 0 16Z" />
                        <path d="M12 8v4l2.6 1.7" />
                    </svg></span>
                <span class="admin-stat-card__trend"></span>
            </div>
            <p>Waktu proses rata-rata</p>
            <strong id="stat-avg-time">-</strong>
            <small>Dari request hingga selesai</small>
        </article>
    </section>

    <section class="admin-dashboard">

        <div class="admin-dashboard__left">

            {{-- PERFORMA --}}
            <article class="admin-panel admin-chart-panel">
                <div class="admin-panel__head">
                    <div>
                        <span class="admin-panel__eyebrow">PERFORMA</span>
                        <h2>Aktivitas transaksi</h2>
                    </div>

                    <select class="admin-select" aria-label="Periode aktivitas transaksi">
                        <option>7 hari terakhir</option>
                        <option>30 hari terakhir</option>
                        <option>Tahun ini</option>
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

                <div id="chart-container"
                    style="height:250px;display:flex;align-items:center;justify-content:center;color:#a0a3ab;border:1px dashed #e0e2e5;border-radius:8px;">
                    [Area Grafik Aktivitas]
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

        <aside class="admin-dashboard__right">

            {{-- PEMBARUAN --}}
            <article class="admin-panel admin-activity-panel">
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

        {{-- <article class="admin-panel admin-quick-panel">
        <div class="admin-panel__head">
            <div>
                <span class="admin-panel__eyebrow">SHORTCUT</span>
                <h2>Aksi cepat</h2>
            </div>
        </div>
        <div class="admin-quick-list">
            <a href="{{ url('/admin/harga') }}" class="admin-quick-link">
                <span class="admin-quick-link__icon admin-quick-link__icon--blue"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 5h14v14H5z" /><path d="M8 9h8M8 13h8M8 17h5" /></svg></span>
                <span><strong>Kelola harga aki</strong><small>Perbarui harga jual berdasarkan wilayah.</small></span>
                <svg class="admin-quick-link__arrow" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6" /></svg>
            </a>
            <a href="{{ url('/admin/gudang') }}" class="admin-quick-link">
                <span class="admin-quick-link__icon admin-quick-link__icon--red"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 10.5L12 4l8 6.5v9H4z" /><path d="M9 19.5v-5h6v5" /></svg></span>
                <span><strong>Kelola gudang</strong><small>Atur titik layanan dan area jangkauan.</small></span>
                <svg class="admin-quick-link__arrow" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6" /></svg>
            </a>
            <a href="{{ url('/admin/pengguna') }}" class="admin-quick-link">
                <span class="admin-quick-link__icon admin-quick-link__icon--blue"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="3.5" /><path d="M5 20c.8-3.2 3.1-5 7-5s6.2 1.8 7 5" /></svg></span>
                <span><strong>Kelola pengguna</strong><small>Undang atau kelola anggota workspace admin.</small></span>
                <svg class="admin-quick-link__arrow" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6" /></svg>
            </a>
        </div>
    </article> --}}

    </section>
@endsection

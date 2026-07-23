<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Dashboard Admin | Pick Up System</title>
        <meta name="description" content="Dashboard administrasi Pick Up System Indoprima Group.">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="admin-shell">
        <div class="admin-layout" data-admin-app>
            <div class="admin-sidebar-overlay" data-sidebar-overlay></div>

            <aside class="admin-sidebar" id="admin-sidebar" aria-label="Navigasi utama admin">
                <div class="admin-sidebar__head">
                    <a href="#dashboard" class="admin-brand" aria-label="Pick Up System admin">
                        <span class="admin-brand__mark" role="img" aria-label="Indoprima Group logo not found">
                            <span class="admin-brand__mark-red"></span>
                            <span class="admin-brand__mark-blue"></span>
                        </span>
                        <span class="admin-brand__copy">
                            <strong>Indoprima Group</strong>
                            <small>Pick Up System</small>
                        </span>
                    </a>
                    <button type="button" class="sidebar-close" data-sidebar-close aria-label="Tutup navigasi">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" /></svg>
                    </button>
                </div>

                <div class="admin-sidebar__section-label">WORKSPACE ADMIN</div>

                <nav class="admin-nav">
                    <a href="#dashboard" class="admin-nav__link is-active" data-nav-link>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="1" /><rect x="13.5" y="3.5" width="7" height="7" rx="1" /><rect x="3.5" y="13.5" width="7" height="7" rx="1" /><rect x="13.5" y="13.5" width="7" height="7" rx="1" /></svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="#transaksi" class="admin-nav__link" data-nav-link>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5.5h16v13H4z" /><path d="M8 9h8M8 13h5" /></svg>
                        <span>Transaksi Masuk</span>
                        <span class="admin-nav__badge">—</span>
                    </a>
                    <a href="#harga" class="admin-nav__link" data-nav-link>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 5h14v14H5z" /><path d="M8 9h8M8 13h8M8 17h5" /></svg>
                        <span>Harga Aki</span>
                    </a>
                    <a href="#gudang" class="admin-nav__link" data-nav-link>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 10.5L12 4l8 6.5v9H4z" /><path d="M9 19.5v-5h6v5M7 10.5h10" /></svg>
                        <span>Gudang &amp; Lokasi</span>
                    </a>
                    <a href="#pengguna" class="admin-nav__link" data-nav-link>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="3.5" /><path d="M5 20c.8-3.2 3.1-5 7-5s6.2 1.8 7 5" /></svg>
                        <span>Pengguna</span>
                    </a>
                    <a href="#laporan" class="admin-nav__link" data-nav-link>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 19V9M12 19V5M19 19v-7" /><path d="M3.5 19.5h17" /></svg>
                        <span>Laporan</span>
                    </a>
                </nav>

                <div class="admin-sidebar__section-label admin-sidebar__section-label--secondary">PENGATURAN</div>

                <nav class="admin-nav">
                    <a href="#pengaturan" class="admin-nav__link" data-nav-link>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 8.5A3.5 3.5 0 1 0 12 15.5A3.5 3.5 0 1 0 12 8.5Z" /><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-1.6 1.6-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6v.2h-2.3v-.2a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1-1.6-1.6.1-.1A1.7 1.7 0 0 0 8.8 15a1.7 1.7 0 0 0-1.6-1H7.1v-2.3h.2a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1 1.6-1.6.1.1a1.7 1.7 0 0 0 1.9.3 1.7 1.7 0 0 0 1-1.6v-.2h2.3v.2a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1 1.6 1.6-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.2V14h-.2a1.7 1.7 0 0 0-1.6 1Z" /></svg>
                        <span>Pengaturan Sistem</span>
                    </a>
                    <a href="#bantuan" class="admin-nav__link" data-nav-link>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8.5" /><path d="M9.7 9.5a2.4 2.4 0 1 1 3.8 1.9c-.9.6-1.5 1-1.5 2.1M12 16.7h.01" /></svg>
                        <span>Pusat Bantuan</span>
                    </a>
                </nav>

                <div class="admin-sidebar__support">
                    <div class="admin-sidebar__support-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M5 5.5h14v10H12l-4 3v-3H5z" /><path d="M8.5 9.5h7M8.5 12.5h4" /></svg>
                    </div>
                    <strong>Butuh bantuan?</strong>
                    <p>Hubungi tim Indoprima Group untuk dukungan sistem.</p>
                    <a href="tel:+62312977777">(+62-31) 2977777</a>
                </div>

                <div class="admin-sidebar__foot">
                    <span>© 2026 Indoprima Group</span>
                    <span>v1.0.0</span>
                </div>
            </aside>

            <div class="admin-main">
                <header class="admin-topbar">
                    <div class="admin-topbar__left">
                        <button type="button" class="sidebar-open" data-sidebar-open aria-label="Buka navigasi">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" /></svg>
                        </button>
                        <div class="admin-breadcrumb">
                            <span>Workspace</span>
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6" /></svg>
                            <strong>Dashboard</strong>
                        </div>
                    </div>

                    <div class="admin-topbar__right">
                        <label class="admin-search" for="admin-search">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.3" /><path d="m16 16 4.3 4.3" /></svg>
                            <input id="admin-search" type="search" placeholder="Cari di dashboard" autocomplete="off">
                            <span class="admin-search__shortcut">⌘ K</span>
                        </label>
                        <button type="button" class="topbar-icon-button" aria-label="Notifikasi">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.5 16.5h11l-1.2-1.8V10a4.3 4.3 0 0 0-8.6 0v4.7z" /><path d="M10 19h4" /></svg>
                            <span class="topbar-icon-button__dot"></span>
                        </button>
                        <span class="topbar-divider" aria-hidden="true"></span>
                        <button type="button" class="admin-profile" data-profile-toggle aria-expanded="false">
                            <span class="admin-profile__avatar">AI</span>
                            <span class="admin-profile__copy">
                                <strong>Admin Indoprima</strong>
                                <small>Administrator</small>
                            </span>
                            <svg class="admin-profile__chevron" viewBox="0 0 24 24" aria-hidden="true"><path d="m7 10 5 5 5-5" /></svg>
                        </button>
                        <div class="admin-profile-menu" data-profile-menu hidden>
                            <a href="#profil">Profil Saya</a>
                            <a href="#pengaturan">Pengaturan</a>
                            <a href="#keluar">Keluar</a>
                        </div>
                    </div>
                </header>

                <main class="admin-content" id="dashboard">
                    <div class="admin-page-heading">
                        <div>
                            <span class="admin-eyebrow">OVERVIEW</span>
                            <h1>Dashboard</h1>
                            <p>Kelola aktivitas Pick Up System dari satu ruang kerja terpusat.</p>
                        </div>
                        <div class="admin-page-heading__actions">
                            <button type="button" class="admin-button admin-button--secondary">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 4.5h14v15H5zM8 8h8M8 12h8M8 16h5" /></svg>
                                Export laporan
                            </button>
                            <button type="button" class="admin-button admin-button--primary">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14" /></svg>
                                Tambah data
                            </button>
                        </div>
                    </div>

                    <section class="admin-stat-grid" aria-label="Ringkasan sistem">
                        <article class="admin-stat-card">
                            <div class="admin-stat-card__head">
                                <span class="admin-stat-card__icon admin-stat-card__icon--blue"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16v12H4zM8 10h8M8 14h5" /></svg></span>
                                <span class="admin-stat-card__trend">—</span>
                            </div>
                            <p>Total transaksi</p>
                            <strong>—</strong>
                            <small>Belum ada data terhubung</small>
                        </article>
                        <article class="admin-stat-card">
                            <div class="admin-stat-card__head">
                                <span class="admin-stat-card__icon admin-stat-card__icon--red"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 4.5h12v15H6z" /><path d="M9 8.5h6M9 12h6M9 15.5h3" /></svg></span>
                                <span class="admin-stat-card__trend">—</span>
                            </div>
                            <p>Menunggu verifikasi</p>
                            <strong>—</strong>
                            <small>Belum ada data terhubung</small>
                        </article>
                        <article class="admin-stat-card">
                            <div class="admin-stat-card__head">
                                <span class="admin-stat-card__icon admin-stat-card__icon--blue"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4.5 7.5h15v11h-15z" /><path d="M8 7.5V5.8h8v1.7M8 12h8M8 15.5h4" /></svg></span>
                                <span class="admin-stat-card__trend">—</span>
                            </div>
                            <p>Nilai penjualan</p>
                            <strong>—</strong>
                            <small>Belum ada data terhubung</small>
                        </article>
                        <article class="admin-stat-card">
                            <div class="admin-stat-card__head">
                                <span class="admin-stat-card__icon admin-stat-card__icon--red"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20a8 8 0 1 0 0-16 8 8 0 0 0 0 16Z" /><path d="M12 8v4l2.6 1.7" /></svg></span>
                                <span class="admin-stat-card__trend">—</span>
                            </div>
                            <p>Waktu proses rata-rata</p>
                            <strong>—</strong>
                            <small>Belum ada data terhubung</small>
                        </article>
                    </section>

                    <section class="admin-dashboard-grid admin-dashboard-grid--main">
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
                            <div class="admin-chart-empty">
                                <div class="admin-empty-icon admin-empty-icon--blue">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 18V6M4 18h16" /><path d="m7 14 3-3 3 2 5-6" /></svg>
                                </div>
                                <strong>Belum ada aktivitas transaksi</strong>
                                <p>Data grafik akan tampil setelah transaksi pertama tercatat.</p>
                            </div>
                        </article>

                        <article class="admin-panel admin-activity-panel">
                            <div class="admin-panel__head">
                                <div>
                                    <span class="admin-panel__eyebrow">PEMBARUAN</span>
                                    <h2>Aktivitas terbaru</h2>
                                </div>
                                <a href="#aktivitas" class="admin-panel__link">Lihat semua</a>
                            </div>
                            <div class="admin-list-empty">
                                <div class="admin-empty-icon admin-empty-icon--red">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 5.5h14v13H5z" /><path d="M8.5 9.5h7M8.5 13h4" /></svg>
                                </div>
                                <strong>Belum ada pembaruan</strong>
                                <p>Aktivitas terbaru akan tampil di area ini.</p>
                            </div>
                        </article>
                    </section>

                    <section class="admin-dashboard-grid admin-dashboard-grid--bottom">
                        <article class="admin-panel admin-table-panel">
                            <div class="admin-panel__head">
                                <div>
                                    <span class="admin-panel__eyebrow">OPERASIONAL</span>
                                    <h2>Pesanan yang memerlukan perhatian</h2>
                                </div>
                                <a href="#transaksi" class="admin-panel__link">Kelola transaksi</a>
                            </div>
                            <div class="admin-table-wrap">
                                <table class="admin-table">
                                    <thead>
                                        <tr>
                                            <th>Nomor transaksi</th>
                                            <th>Penjual</th>
                                            <th>Metode penyerahan</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="4">
                                                <div class="admin-table-empty">
                                                    <strong>Belum ada pesanan</strong>
                                                    <span>Pesanan baru akan tampil setelah data tersedia.</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </article>

                        <article class="admin-panel admin-quick-panel">
                            <div class="admin-panel__head">
                                <div>
                                    <span class="admin-panel__eyebrow">SHORTCUT</span>
                                    <h2>Aksi cepat</h2>
                                </div>
                            </div>
                            <div class="admin-quick-list">
                                <a href="#harga" class="admin-quick-link">
                                    <span class="admin-quick-link__icon admin-quick-link__icon--blue"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 5h14v14H5z" /><path d="M8 9h8M8 13h8M8 17h5" /></svg></span>
                                    <span><strong>Kelola harga aki</strong><small>Perbarui harga jual berdasarkan wilayah.</small></span>
                                    <svg class="admin-quick-link__arrow" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6" /></svg>
                                </a>
                                <a href="#gudang" class="admin-quick-link">
                                    <span class="admin-quick-link__icon admin-quick-link__icon--red"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 10.5L12 4l8 6.5v9H4z" /><path d="M9 19.5v-5h6v5" /></svg></span>
                                    <span><strong>Tambah gudang</strong><small>Atur titik layanan dan area jangkauan.</small></span>
                                    <svg class="admin-quick-link__arrow" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6" /></svg>
                                </a>
                                <a href="#pengguna" class="admin-quick-link">
                                    <span class="admin-quick-link__icon admin-quick-link__icon--blue"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="3.5" /><path d="M5 20c.8-3.2 3.1-5 7-5s6.2 1.8 7 5" /></svg></span>
                                    <span><strong>Tambah pengguna</strong><small>Undang anggota baru ke workspace admin.</small></span>
                                    <svg class="admin-quick-link__arrow" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6" /></svg>
                                </a>
                            </div>
                        </article>
                    </section>
                </main>

                <footer class="admin-footer">
                    <div class="admin-footer__brand">
                        <span class="admin-brand__mark admin-brand__mark--small" role="img" aria-label="Indoprima Group logo not found">
                            <span class="admin-brand__mark-red"></span>
                            <span class="admin-brand__mark-blue"></span>
                        </span>
                        <span>Pick Up System · Indoprima Group</span>
                    </div>
                    <div class="admin-footer__contact">
                        <span>HEADQUARTERS</span>
                        <span>Jl. Gardu Induk PLN No. 5, Margomulyo, Tandes Lor, Kec. Tandes, Surabaya 60187 – Indonesia</span>
                        <a href="tel:+62312977777">(+62-31) 2977777</a>
                        <a href="mailto:hrd@imligroup.com">hrd@imligroup.com</a>
                        <a href="https://www.instagram.com/imli_official/" target="_blank" rel="noopener noreferrer">Instagram</a>
                    </div>
                </footer>
            </div>
        </div>
    </body>
</html>

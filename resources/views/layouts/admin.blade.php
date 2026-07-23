<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') | Pick Up System</title>
    <meta name="description" content="Dashboard administrasi Pick Up System Indoprima Group.">
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin-api.js'])
</head>

<body class="admin-shell">
    <div class="admin-layout" data-admin-app>
        <div class="admin-sidebar-overlay" data-sidebar-overlay></div>

        <aside class="admin-sidebar" id="admin-sidebar" aria-label="Navigasi utama admin">
            <div class="admin-sidebar__head">
                <a href="{{ url('/admin/dashboard') }}" class="admin-brand" aria-label="Pick Up System admin">
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
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6 6l12 12M18 6L6 18" />
                    </svg>
                </button>
            </div>

            <div class="admin-sidebar__section-label">WORKSPACE ADMIN</div>

            <nav class="admin-nav">
                <a href="{{ url('/admin/dashboard') }}"
                    class="admin-nav__link {{ request()->is('admin/dashboard') ? 'is-active' : '' }}" data-nav-link>
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <rect x="3.5" y="3.5" width="7" height="7" rx="1" />
                        <rect x="13.5" y="3.5" width="7" height="7" rx="1" />
                        <rect x="3.5" y="13.5" width="7" height="7" rx="1" />
                        <rect x="13.5" y="13.5" width="7" height="7" rx="1" />
                    </svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ url('/admin/transaksi') }}"
                    class="admin-nav__link {{ request()->is('admin/transaksi') ? 'is-active' : '' }}" data-nav-link>
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 5.5h16v13H4z" />
                        <path d="M8 9h8M8 13h5" />
                    </svg>
                    <span>Transaksi Masuk</span>
                </a>
                <a href="{{ url('/admin/harga') }}"
                    class="admin-nav__link {{ request()->is('admin/harga') ? 'is-active' : '' }}" data-nav-link>
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M5 5h14v14H5z" />
                        <path d="M8 9h8M8 13h8M8 17h5" />
                    </svg>
                    <span>Harga Aki</span>
                </a>
                <a href="{{ url('/admin/gudang') }}"
                    class="admin-nav__link {{ request()->is('admin/gudang') ? 'is-active' : '' }}" data-nav-link>
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 10.5L12 4l8 6.5v9H4z" />
                        <path d="M9 19.5v-5h6v5M7 10.5h10" />
                    </svg>
                    <span>Gudang &amp; Lokasi</span>
                </a>
                <a href="{{ url('/admin/pengguna') }}"
                    class="admin-nav__link {{ request()->is('admin/pengguna') ? 'is-active' : '' }}" data-nav-link>
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="8" r="3.5" />
                        <path d="M5 20c.8-3.2 3.1-5 7-5s6.2 1.8 7 5" />
                    </svg>
                    <span>Pengguna</span>
                </a>
                <a href="{{ url('/admin/laporan') }}"
                    class="admin-nav__link {{ request()->is('admin/laporan') ? 'is-active' : '' }}" data-nav-link>
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M5 19V9M12 19V5M19 19v-7" />
                        <path d="M3.5 19.5h17" />
                    </svg>
                    <span>Laporan</span>
                </a>
            </nav>

            <div class="admin-sidebar__foot">
                <span>© 2026 Indoprima Group</span>
                <span>v1.0.0</span>
            </div>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <div class="admin-topbar__left">
                    <button type="button" class="sidebar-open" data-sidebar-open aria-label="Buka navigasi">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M4 7h16M4 12h16M4 17h16" />
                        </svg>
                    </button>
                    <div class="admin-breadcrumb">
                        <span>Workspace</span>
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="m9 18 6-6-6-6" />
                        </svg>
                        <strong>@yield('title', 'Dashboard')</strong>
                    </div>
                </div>

                <div class="admin-topbar__right">
                    <!--
                        <label class="admin-search" for="admin-search">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.3" /><path d="m16 16 4.3 4.3" /></svg>
                            <input id="admin-search" type="search" placeholder="Cari di dashboard" autocomplete="off">
                            <span class="admin-search__shortcut">⌘ K</span>
                        </label>
                        -->
                    <button type="button" class="topbar-icon-button" aria-label="Notifikasi">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M6.5 16.5h11l-1.2-1.8V10a4.3 4.3 0 0 0-8.6 0v4.7z" />
                            <path d="M10 19h4" />
                        </svg>
                        <span class="topbar-icon-button__dot"></span>
                    </button>
                    <span class="topbar-divider" aria-hidden="true"></span>
                    <button type="button" class="admin-profile" id="admin-profile-btn" aria-expanded="false">
                        <span class="admin-profile__avatar" id="auth-user-initial">A</span>
                        <span class="admin-profile__copy">
                            <strong id="auth-user-name">Admin</strong>
                            <small>Administrator</small>
                        </span>
                        <svg class="admin-profile__chevron" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="m7 10 5 5 5-5" />
                        </svg>
                    </button>
                    <div class="admin-profile-menu" id="admin-profile-menu" hidden>
                        <a href="#">Profil Saya</a>
                        <a href="#">Pengaturan</a>
                        <a href="#" id="btn-logout">Keluar</a>
                    </div>
                </div>
            </header>

            <main class="admin-content" id="main-content">
                @yield('content')
            </main>
        </div>
    </div>
</body>

</html>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') | Pick Up System</title>
    <!-- Leaflet OpenStreetMap CDN -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin-api.js'])
    <script>
        if (localStorage.getItem('admin_theme') === 'dark') {
            document.documentElement.classList.add('admin-dark-mode');
        }
    </script>
</head>

<body class="admin-shell">
    <div class="admin-layout" data-admin-app>
        <div class="admin-sidebar-overlay" data-sidebar-overlay></div>

        <aside class="admin-sidebar" id="admin-sidebar" aria-label="Navigasi utama admin">
            <div class="admin-sidebar__head">
                <a href="{{ url('/admin/dashboard') }}" class="admin-brand" aria-label="Pick Up System admin">

                    <img src="{{ Vite::asset('resources/img/indoprima_logo.png') }}" alt="Indoprima Group"
                        class="admin-brand__logo" style=>

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
                <button type="button" class="admin-theme-toggle-foot" id="admin-theme-toggle" aria-label="Toggle Dark Mode">
                    <svg class="admin-theme-icon--sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="5"/>
                        <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
                    </svg>
                    <svg class="admin-theme-icon--moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                    <span id="admin-theme-text">Mode Gelap</span>
                </button>
                <div class="admin-sidebar__foot-copy">
                    <span>© 2026 Indoprima Group</span>
                    <span>v1.0.0</span>
                </div>
            </div>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <div class="admin-topbar__left">
                    <button id="sidebarToggle" class="sidebar-toggle" type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
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

                <div class="admin-topbar__right" style="display:flex; align-items:center; gap:16px;">
                    <!-- Live Real-Time Clock Widget -->
                    <div id="live-clock-widget" style="display:flex; align-items:center; gap:8px; background:#f8fafc; border:1px solid #e2e8f0; padding:6px 14px; border-radius:20px; font-size:12px; color:#475569;">
                        <!-- <span style="width:8px; height:8px; background:#10b981; border-radius:50%; box-shadow:0 0 6px rgba(16,185,129,0.6); display:inline-block;" title="System Online"></span> -->
                        <span id="admin-live-clock">Memuat jam...</span>
                    </div>

                    <button type="button" class="admin-profile" id="admin-profile-btn" aria-expanded="false">
                        <span class="admin-profile__avatar" id="auth-user-initial">A</span>
                        <span class="admin-profile__copy">
                            <strong id="auth-user-name">Admin</strong>
                            <small>Administrator</small>
                        </span>
                    </button>
                    <div class="admin-profile-menu" id="admin-profile-menu" hidden>
                        <a href="#" id="btn-edit-profile">Edit Profil</a>
                        <a href="#" id="btn-logout">Keluar</a>
                    </div>
                </div>
            </header>

            <main class="admin-content" id="main-content">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Custom Toast Floating Notification -->
    <div id="admin-toast"
        style="display:none; position:fixed; top:24px; right:24px; z-index:999; background:#10b981; color:#fff; padding:12px 20px; border-radius:8px; box-shadow:0 10px 25px rgba(0,0,0,0.18); font-size:13px; font-weight:600; align-items:center; gap:10px; opacity:0; transition:opacity .25s ease;">
        <span id="admin-toast-icon">✓</span>
        <span id="admin-toast-message">Notifikasi</span>
    </div>

    <!-- Custom Modal Konfirmasi (Ganti confirm bawaan browser) -->
    <div id="modal-custom-confirm"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:200; align-items:center; justify-content:center;">
        <div class="admin-panel" style="width:380px; text-align:center;">
            <div style="margin-bottom:14px;">
                <h3 id="confirm-title" style="font-size:16px; color:#111318; margin-bottom:8px; font-weight:700;">
                    Konfirmasi Hapus</h3>
                <p id="confirm-message" style="font-size:13px; color:#6d727c; margin:0; line-height:1.4;"></p>
            </div>
            <div style="display:flex; gap:10px; justify-content:center; margin-top:20px;">
                <button type="button" id="btn-confirm-cancel" class="admin-button admin-button--secondary"
                    style="width:100px;">Batal</button>
                <button type="button" id="btn-confirm-ok" class="admin-button admin-button--primary"
                    style="width:110px; background:#ba1b2b;">Ya, Hapus</button>
            </div>
        </div>
    </div>

    <!-- Modal Edit Profil Admin -->
    <div id="modal-edit-profile"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:110; align-items:center; justify-content:center;">
        <div class="admin-panel" style="width:420px;">
            <div class="admin-panel__head">
                <h2>Edit Profil Admin</h2>
            </div>
            <form id="form-edit-profile">
                <div style="margin-bottom:14px;">
                    <label
                        style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Nama
                        Admin</label>
                    <input type="text" id="profile-name" class="admin-select"
                        style="width:100%; padding:8px 10px; border-radius:6px;" required>
                </div>
                <div style="margin-bottom:14px;">
                    <label
                        style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Password
                        Saat Ini (Wajib jika ganti password)</label>
                    <input type="password" id="profile-current-password" class="admin-select"
                        style="width:100%; padding:8px 10px; border-radius:6px;" placeholder="Password lama">
                </div>
                <div style="margin-bottom:18px;">
                    <label
                        style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Password
                        Baru (Opsional)</label>
                    <input type="password" id="profile-new-password" class="admin-select"
                        style="width:100%; padding:8px 10px; border-radius:6px;"
                        placeholder="Kosongkan jika tidak diubah">
                </div>
                <div id="profile-error" style="display:none; color:#ba1b2b; font-size:12px; margin-bottom:14px;">
                </div>
                <div style="display:flex; gap:10px; justify-content:flex-end;">
                    <button type="button" class="admin-button admin-button--secondary"
                        onclick="document.getElementById('modal-edit-profile').style.display='none'">Batal</button>
                    <button type="submit" class="admin-button admin-button--primary">Simpan Profil</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi Logout dengan Password -->
    <div id="modal-logout-confirm"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:110; align-items:center; justify-content:center;">
        <div class="admin-panel" style="width:400px;">
            <div class="admin-panel__head">
                <h2>Konfirmasi Keluar</h2>
            </div>
            <form id="form-logout-confirm">
                <p style="font-size:13px; color:#4a4f59; margin-bottom:14px;">Masukkan password Anda untuk
                    mengonfirmasi keluar dari sistem.</p>
                <div style="margin-bottom:16px;">
                    <label
                        style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Password
                        Admin</label>
                    <input type="password" id="logout-password" class="admin-select"
                        style="width:100%; padding:8px 10px; border-radius:6px;" placeholder="Masukkan password Anda"
                        required>
                </div>
                <div id="logout-error" style="display:none; color:#ba1b2b; font-size:12px; margin-bottom:14px;"></div>
                <div style="display:flex; gap:10px; justify-content:flex-end;">
                    <button type="button" class="admin-button admin-button--secondary"
                        onclick="document.getElementById('modal-logout-confirm').style.display='none'">Batal</button>
                    <button type="submit" class="admin-button admin-button--primary"
                        style="background:#ba1b2b;">Keluar</button>
                </div>
            </form>
        </div>
    </div>
</body>
<script>
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.admin-sidebar');
    const footer = document.querySelector('.admin-footer');

    function updateSidebar(collapsed) {
        sidebar.classList.toggle('collapsed', collapsed);
        if (footer) {
            footer.classList.toggle('expanded', collapsed);
        }
        localStorage.setItem('sidebarCollapsed', collapsed);
    }

    const savedState = localStorage.getItem('sidebarCollapsed') === 'true';
    updateSidebar(savedState);

    toggle.addEventListener('click', () => {
        updateSidebar(!sidebar.classList.contains('collapsed'));
    });

    const themeToggleBtn = document.getElementById('admin-theme-toggle');
    const themeText = document.getElementById('admin-theme-text');

    function applyAdminTheme(isDark) {
        document.documentElement.classList.toggle('admin-dark-mode', isDark);
        if (document.body) {
            document.body.classList.toggle('admin-dark-mode', isDark);
        }
        localStorage.setItem('admin_theme', isDark ? 'dark' : 'light');
        if (themeText) {
            themeText.textContent = isDark ? 'Mode Terang' : 'Mode Gelap';
        }
        if (typeof window.switchOrderTab === 'function' && typeof activeStatus !== 'undefined') {
            window.switchOrderTab(activeStatus);
        }
        if (typeof window.loadReportAnalytic === 'function') {
            window.loadReportAnalytic();
        }
    }

    const initialIsDark = localStorage.getItem('admin_theme') === 'dark';
    applyAdminTheme(initialIsDark);

    themeToggleBtn?.addEventListener('click', () => {
        const isCurrentDark = document.documentElement.classList.contains('admin-dark-mode');
        applyAdminTheme(!isCurrentDark);
    });
</script>

</html>

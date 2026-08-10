<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Dashboard') | Pick Up System</title>
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
                    <a href="{{ url('/admin/dashboard') }}" class="admin-brand" aria-label="One Stop Solution admin">

                        <img src="{{ asset('img/logo_admin2-removebg-preview.png') }}" alt="Modern Mulya Mandiri"
                            class="admin-brand__logo" style="height: 40px; width: auto; object-fit: contain;">

                        <span class="admin-brand__copy">
                            <strong>Modern Mulya Mandiri</strong>
                            <small>One Stop Solution</small>
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
                        class="admin-nav__link {{ request()->is('admin/dashboard') ? 'is-active' : '' }}" data-nav-link
                        data-tooltip="Dashboard">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="3.5" y="3.5" width="7" height="7" rx="1" />
                            <rect x="13.5" y="3.5" width="7" height="7" rx="1" />
                            <rect x="3.5" y="13.5" width="7" height="7" rx="1" />
                            <rect x="13.5" y="13.5" width="7" height="7" rx="1" />
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ url('/admin/transaksi') }}"
                        class="admin-nav__link {{ request()->is('admin/transaksi') ? 'is-active' : '' }}" data-nav-link
                        data-tooltip="Transaksi Masuk">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M4 5.5h16v13H4z" />
                            <path d="M8 9h8M8 13h5" />
                        </svg>
                        <span>Transaksi Masuk</span>
                    </a>
                    <a href="{{ url('/admin/harga') }}"
                        class="admin-nav__link {{ request()->is('admin/harga') ? 'is-active' : '' }}" data-nav-link
                        data-tooltip="Harga Aki">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M5 5h14v14H5z" />
                            <path d="M8 9h8M8 13h8M8 17h5" />
                        </svg>
                        <span>Harga Aki</span>
                    </a>
                    <a href="{{ url('/admin/gudang') }}"
                        class="admin-nav__link {{ request()->is('admin/gudang') ? 'is-active' : '' }}" data-nav-link
                        data-tooltip="Gudang &amp; Lokasi">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M4 10.5L12 4l8 6.5v9H4z" />
                            <path d="M9 19.5v-5h6v5M7 10.5h10" />
                        </svg>
                        <span>Gudang &amp; Lokasi</span>
                    </a>
                    <a href="{{ url('/admin/laporan') }}"
                        class="admin-nav__link {{ request()->is('admin/laporan') ? 'is-active' : '' }}" data-nav-link
                        data-tooltip="Laporan">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M5 19V9M12 19V5M19 19v-7" />
                            <path d="M3.5 19.5h17" />
                        </svg>
                        <span>Laporan</span>
                    </a>
                </nav>

                <div class="admin-sidebar__foot">
                    <button type="button" class="admin-theme-toggle-foot" id="admin-theme-toggle"
                        aria-label="Toggle Dark Mode">
                        <svg class="admin-theme-icon--sun" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="5" />
                            <path
                                d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" />
                        </svg>
                        <svg class="admin-theme-icon--moon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
                        </svg>
                        <span id="admin-theme-text">Mode Gelap</span>
                    </button>
                    <div class="admin-sidebar__foot-copy">
                        <span>© 2026 Modern Mulya Mandiri</span>
                        <span>v1.0.0</span>
                    </div>
                </div>
            </aside>

            <div class="admin-main">
                <header class="admin-topbar">
                    <div class="admin-topbar__left">
                        <button id="sidebarToggle" class="sidebar-toggle" type="button" data-sidebar-open>
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
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
                        <!-- Global Language Switcher -->
                        <div class="lang-switch" data-lang-switch>
                            <button type="button" class="lang-btn is-active" data-lang-btn="id" aria-label="Bahasa Indonesia">ID</button>
                            <span class="lang-divider">|</span>
                            <button type="button" class="lang-btn" data-lang-btn="en" aria-label="English Language">EN</button>
                        </div>

                        <div id="live-clock-widget"
                            style="display:flex; align-items:center; gap:8px; background:#f8fafc; border:1px solid #e2e8f0; padding:6px 14px; border-radius:20px; font-size:12px; color:#475569;">
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

        <div id="admin-toast"
            style="display:none; position:fixed; top:24px; right:24px; z-index:999999; background:#10b981; color:#fff; padding:14px 22px; border-radius:10px; box-shadow:0 12px 35px rgba(0,0,0,0.3); font-size:14px; font-weight:700; align-items:center; gap:10px; opacity:0; transform:translateY(-10px); transition:opacity 0.3s ease, transform 0.3s ease;">
            <span id="admin-toast-icon" style="font-size:16px;">✓</span>
            <span id="admin-toast-message">Notifikasi</span>
        </div>

        {{-- New Order / Activity Notification Popup --}}
        <div id="order-notif-popup" style="
            display: none;
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 998;
            width: 340px;
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.18), 0 4px 16px rgba(37,99,235,0.12);
            border: 1px solid #e0e7ff;
            overflow: hidden;
            opacity: 0;
            transform: translateX(30px) scale(0.97);
            transition: opacity 0.3s cubic-bezier(0.4,0,0.2,1), transform 0.3s cubic-bezier(0.4,0,0.2,1);
            pointer-events: auto;
        ">
            {{-- Accent bar top --}}
            <div style="height:4px; background: linear-gradient(90deg, #2563eb 0%, #7c3aed 100%);"></div>

            {{-- Content --}}
            <div style="padding: 16px 18px 14px 18px;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:10px;">
                    <div style="display:flex; align-items:flex-start; gap:12px; flex:1; min-width:0;">
                        <div style="
                            width: 38px; height: 38px; flex-shrink:0;
                            background: linear-gradient(135deg, #eff6ff 0%, #e0e7ff 100%);
                            border-radius: 10px;
                            display:flex; align-items:center; justify-content:center;
                            font-size: 18px;
                        " id="order-notif-icon-wrap">🔔</div>
                        <div style="flex:1; min-width:0;">
                            <p id="order-notif-title" style="
                                font-size:13px; font-weight:700; color:#111827;
                                margin:0 0 4px 0; line-height:1.3;
                                white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
                            ">Notifikasi Baru</p>
                            <p id="order-notif-body" style="
                                font-size:12px; color:#6b7280; margin:0; line-height:1.5;
                                display:-webkit-box; -webkit-line-clamp:2;
                                -webkit-box-orient:vertical; overflow:hidden;
                            ">Ada aktivitas terbaru di sistem.</p>
                        </div>
                    </div>
                    <button type="button" id="order-notif-close" aria-label="Tutup notifikasi" style="
                        flex-shrink:0;
                        width:26px; height:26px;
                        border:none; background:#f3f4f6; color:#6b7280;
                        border-radius:6px; cursor:pointer;
                        font-size:14px; font-weight:700;
                        display:flex; align-items:center; justify-content:center;
                        transition: background 0.15s, color 0.15s;
                        margin-top:-2px;
                    " onmouseover="this.style.background='#fee2e2';this.style.color='#dc2626';"
                       onmouseout="this.style.background='#f3f4f6';this.style.color='#6b7280';">×</button>
                </div>

                <button type="button" id="order-notif-cta" style="
                    margin-top:14px; width:100%;
                    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
                    color:#fff; border:none; border-radius:8px;
                    padding:9px 16px; font-size:12px; font-weight:600;
                    cursor:pointer; display:flex; align-items:center; justify-content:center; gap:6px;
                    transition: opacity 0.15s, transform 0.15s;
                " onmouseover="this.style.opacity='0.9';this.style.transform='scale(1.01)'"
                   onmouseout="this.style.opacity='1';this.style.transform='scale(1)'">
                    <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:none;stroke:currentColor;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round;" aria-hidden="true"><path d="M4 5.5h16v13H4z"/><path d="M8 9h8M8 13h5"/></svg>
                    Lihat Detail Transaksi
                </button>

                {{-- Progress bar countdown --}}
                <div style="margin-top:10px; height:3px; background:#f3f4f6; border-radius:99px; overflow:hidden;">
                    <div id="order-notif-progress" style="
                        height:100%; width:100%;
                        background: linear-gradient(90deg, #2563eb, #7c3aed);
                        border-radius:99px;
                        transition: width 0.1s linear;
                    "></div>
                </div>
                <p id="order-notif-timer-text" style="font-size:10px;color:#9ca3af;margin:4px 0 0;text-align:right;">Menutup dalam 5 detik...</p>
            </div>
        </div>

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
                        style="width:110px; background:#ba1b2b;">Hapus</button>
                </div>
            </div>
        </div>

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
                            style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Kata Sandi
                            Saat Ini (Wajib jika ganti kata sandi)</label>
                        <input type="password" id="profile-current-password" class="admin-select"
                            style="width:100%; padding:8px 10px; border-radius:6px;" placeholder="Kata sandi lama">
                    </div>
                    <div style="margin-bottom:18px;">
                        <label
                            style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Kata Sandi
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

        <div id="modal-logout-confirm"
            style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:110; align-items:center; justify-content:center;">
            <div class="admin-panel" style="width:400px;">
                <div class="admin-panel__head">
                    <h2>Konfirmasi Keluar</h2>
                </div>
                <form id="form-logout-confirm">
                    <p style="font-size:13px; color:#4a4f59; margin-bottom:14px;">Masukkan kata sandi Anda untuk
                        mengonfirmasi keluar dari sistem.</p>
                    <div style="margin-bottom:16px;">
                        <label
                            style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Kata Sandi
                            Admin</label>
                        <input type="password" id="logout-password" class="admin-select"
                            style="width:100%; padding:8px 10px; border-radius:6px;"
                            placeholder="Masukkan kata sandi Anda" required>
                    </div>
                    <div id="logout-error" style="display:none; color:#ba1b2b; font-size:12px; margin-bottom:14px;">
                    </div>
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
        const sidebar = document.querySelector('.admin-sidebar');
        const footer = document.querySelector('.admin-footer');
        let hoverTimeout = null;
        let autoExpanded = false;

        function updateSidebar(collapsed, save = true) {
            sidebar.classList.toggle('collapsed', collapsed);
            if (footer) {
                footer.classList.toggle('expanded', collapsed);
            }
            if (save) {
                localStorage.setItem('sidebarCollapsed', collapsed);
            }
        }

        const savedState = localStorage.getItem('sidebarCollapsed') !== 'false';
        updateSidebar(savedState);

        const sidebarToggleBtn = document.getElementById('sidebarToggle');
        if (sidebarToggleBtn) {
            sidebarToggleBtn.addEventListener('click', () => {
                const isCurrentlyCollapsed = sidebar.classList.contains('collapsed');
                updateSidebar(!isCurrentlyCollapsed);
                autoExpanded = false; // reset hover state on manual click
            });
        }

        if (window.matchMedia('(hover: hover)').matches) {
            sidebar.addEventListener('mouseenter', () => {
                if (sidebar.classList.contains('collapsed')) {
                    hoverTimeout = setTimeout(() => {
                        updateSidebar(false, false);
                        autoExpanded = true;
                    }, 1500);
                }
            });

            sidebar.addEventListener('mouseleave', () => {
                if (hoverTimeout) {
                    clearTimeout(hoverTimeout);
                    hoverTimeout = null;
                }
                if (autoExpanded) {
                    updateSidebar(true, false);
                    autoExpanded = false;
                }
            });
        }

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
            if (typeof window.updateOrderTabAppearance === 'function') {
                window.updateOrderTabAppearance();
            }
            if (typeof window.__renderGeneralPagination === 'function' && window.__adminPaginationState) {
                Object.entries(window.__adminPaginationState).forEach(([containerId, state]) => {
                    if (document.getElementById(containerId)) {
                        window.__renderGeneralPagination(state.pagination, containerId, state.onClickFnName);
                    }
                });
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
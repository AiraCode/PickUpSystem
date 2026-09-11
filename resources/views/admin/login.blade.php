<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login Admin | One Stop Solution Modern Mulya Mandiri</title>
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin-api.js'])
    </head>

    <body class="admin-login-body">
        <div class="bg-glow-1"></div>
        <div class="bg-glow-2"></div>

        <div class="admin-dark-mode" style="position: absolute; top: 20px; right: 20px; z-index: 10;">
            <div class="lang-switch" data-lang-switch>
                <button type="button" class="lang-btn is-active" data-lang-btn="id" aria-label="Bahasa Indonesia">ID</button>
                <span class="lang-divider">|</span>
                <button type="button" class="lang-btn" data-lang-btn="en" aria-label="English Language">EN</button>
            </div>
        </div>

        <div class="login-wrapper">
            <div class="login-card">
                <div class="login-brand">
                    <div class="brand-logo-wrap">
                        <img src="{{ asset('img/logo_admin2-removebg-preview.png') }}" alt="Modern Mulya Mandiri Logo">
                    </div>
                    <span class="brand-kicker">MODERN MULYA MANDIRI</span>
                    <h1>One Stop Solution</h1>
                    <p>Portal Verifikasi & Kelola Transaksi Admin</p>
                </div>

                <div class="error-message" id="login-error"></div>

                <form id="login-form">
                    <div class="form-group">
                        <label class="form-label" for="name">Nama Admin</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <input type="text" id="name" class="form-input" placeholder="Masukkan nama admin" required autocomplete="username">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Kata Sandi</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            <input type="password" id="password" class="form-input" placeholder="Masukkan kata sandi" required autocomplete="current-password">
                        </div>
                    </div>

                    <button type="submit" class="btn-login" id="btn-submit">
                        <span>Masuk ke Dashboard</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </button>
                </form>

                <div class="login-footer-info">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                    <span>Sistem Terenkripsi & Verifikasi Internal</span>
                </div>
            </div>
        </div>
    </body>

</html>

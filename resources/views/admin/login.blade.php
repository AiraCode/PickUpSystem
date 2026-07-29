<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login Admin | One Stop Solution Modern Mulya Mandiri</title>
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin-api.js'])
        <style>
            :root {
                --brand-blue: #2250fc;
                --brand-red: #ba1b2b;
                --brand-dark: #0f172a;
                --brand-slate: #1e293b;
                --text-main: #0f172a;
                --text-muted: #64748b;
            }

            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }

            @keyframes loginBgGradient {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }

            body {
                background: linear-gradient(-45deg, #0f172a, #1e3a8a, #4c1d95, #7f1d1d, #0f172a);
                background-size: 400% 400%;
                animation: loginBgGradient 25s ease infinite;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                padding: 24px;
                font-family: 'Inter', system-ui, -apple-system, sans-serif;
            }

            .bg-glow-1 {
                position: absolute;
                top: 10%;
                left: 15%;
                width: 300px;
                height: 300px;
                background: rgba(34, 80, 252, 0.15);
                filter: blur(100px);
                border-radius: 50%;
                pointer-events: none;
            }

            .bg-glow-2 {
                position: absolute;
                bottom: 10%;
                right: 15%;
                width: 350px;
                height: 350px;
                background: rgba(186, 27, 43, 0.12);
                filter: blur(120px);
                border-radius: 50%;
                pointer-events: none;
            }

            .login-wrapper {
                position: relative;
                z-index: 10;
                width: 100%;
                max-width: 420px;
            }

            .login-card {
                background: rgba(255, 255, 255, 0.96);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 1px solid rgba(255, 255, 255, 0.8);
                border-radius: 24px;
                padding: 40px 32px;
                box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3), 0 1px 2px rgba(255, 255, 255, 0.5) inset;
            }

            .login-brand {
                text-align: center;
                margin-bottom: 32px;
            }

            .brand-logo-wrap {
                width: 72px;
                height: 72px;
                background: #f8fafc;
                border-radius: 20px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 16px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
                border: 1px solid #e2e8f0;
            }

            .brand-logo-wrap img {
                height: 48px;
                width: auto;
                object-fit: contain;
            }

            .brand-kicker {
                display: block;
                font-size: 11px;
                font-weight: 800;
                letter-spacing: 1.5px;
                color: var(--brand-blue);
                text-transform: uppercase;
                margin-bottom: 4px;
            }

            .login-brand h1 {
                font-size: 24px;
                font-weight: 800;
                color: var(--brand-dark);
                letter-spacing: -0.5px;
                margin-bottom: 6px;
            }

            .login-brand p {
                font-size: 13px;
                color: var(--text-muted);
            }

            .form-group {
                margin-bottom: 20px;
            }

            .form-label {
                display: block;
                font-size: 12px;
                font-weight: 700;
                color: var(--brand-slate);
                margin-bottom: 8px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .input-wrapper {
                position: relative;
                display: flex;
                align-items: center;
            }

            .input-icon {
                position: absolute;
                left: 14px;
                width: 18px;
                height: 18px;
                color: #94a3b8;
                transition: color 0.2s;
            }

            .form-input {
                width: 100%;
                padding: 13px 14px 13px 44px;
                font-size: 14px;
                border: 1.5px solid #e2e8f0;
                border-radius: 12px;
                outline: none;
                background: #f8fafc;
                color: var(--text-main);
                font-weight: 500;
                transition: all 0.2s;
            }

            .form-input:focus {
                border-color: var(--brand-blue);
                background: #ffffff;
                box-shadow: 0 0 0 4px rgba(34, 80, 252, 0.1);
            }

            .form-input:focus + .input-icon,
            .input-wrapper:focus-within .input-icon {
                color: var(--brand-blue);
            }

            .error-message {
                display: none;
                background: #fef2f2;
                border: 1px solid #fecaca;
                color: #dc2626;
                font-size: 13px;
                padding: 12px 14px;
                margin-bottom: 20px;
                border-radius: 10px;
                animation: fadeIn 0.3s ease;
            }

            .btn-login {
                width: 100%;
                background: linear-gradient(135deg, var(--brand-blue) 0%, #1d40d8 100%);
                color: #ffffff;
                border: none;
                border-radius: 12px;
                padding: 14px;
                font-size: 14px;
                font-weight: 700;
                cursor: pointer;
                box-shadow: 0 8px 20px rgba(34, 80, 252, 0.3);
                transition: all 0.2s;
                margin-top: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }

            .btn-login:hover {
                transform: translateY(-1px);
                box-shadow: 0 12px 24px rgba(34, 80, 252, 0.4);
            }

            .btn-login svg {
                width: 18px;
                height: 18px;
            }

            .login-footer-info {
                margin-top: 26px;
                padding-top: 20px;
                border-top: 1px solid #f1f5f9;
                text-align: center;
                font-size: 11px;
                color: var(--text-muted);
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
            }

            .login-footer-info svg {
                width: 14px;
                height: 14px;
                color: #10b981;
            }

            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(-4px); }
                to { opacity: 1; transform: translateY(0); }
            }
        </style>
    </head>

    <body>
        <div class="bg-glow-1"></div>
        <div class="bg-glow-2"></div>

        <div class="login-wrapper">
            <div class="login-card">
                <div class="login-brand">
                    <div class="brand-logo-wrap">
                        <img src="{{ Vite::asset('resources/img/logo_admin2-removebg-preview.png') }}" alt="Modern Mulya Mandiri Logo">
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
                        <label class="form-label" for="password">Password</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            <input type="password" id="password" class="form-input" placeholder="Masukkan password" required autocomplete="current-password">
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

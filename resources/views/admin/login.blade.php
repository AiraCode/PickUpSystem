<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login | Pick Up System</title>
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin-api.js'])
        <style>
            body {
                background: #f5f6f8;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                margin: 0;
                font-family: 'Inter', sans-serif;
            }

            .login-card {
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 14px;
                padding: 40px;
                width: 100%;
                max-width: 400px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            }

            .login-header {
                text-align: center;
                margin-bottom: 30px;
            }

            .login-header h1 {
                font-size: 24px;
                font-weight: 800;
                color: #111318;
                margin-bottom: 5px;
            }

            .login-header p {
                font-size: 13px;
                color: #6d727c;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .form-label {
                display: block;
                font-size: 12px;
                font-weight: 700;
                color: #111318;
                margin-bottom: 8px;
            }

            .form-input {
                width: 100%;
                height: 42px;
                padding: 0 12px;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                font-size: 13px;
                outline: none;
                transition: border-color 0.2s;
            }

            .form-input:focus {
                border-color: #2250fc;
            }

            .btn-login {
                width: 100%;
                height: 42px;
                background: #2250fc;
                color: #fff;
                border: none;
                border-radius: 8px;
                font-size: 13px;
                font-weight: 700;
                cursor: pointer;
                transition: background 0.2s;
            }

            .btn-login:hover {
                background: #ba1b2b;
            }

            .error-message {
                color: #ba1b2b;
                font-size: 12px;
                margin-bottom: 15px;
                display: none;
                text-align: center;
                background: #fff0f1;
                padding: 10px;
                border-radius: 6px;
            }
        </style>
    </head>

    <body>
        <div class="login-card">
            <div class="login-header">
                <h1>Pick Up System</h1>
                <p>Masuk ke Workspace Admin</p>
            </div>
            <div class="error-message" id="login-error"></div>
            <form id="login-form">
                <div class="form-group">
                    <label class="form-label" for="name">Nama Admin</label>
                    <input type="text" id="name" class="form-input" placeholder="Masukkan nama admin" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" class="form-input" placeholder="Masukkan password" required>
                </div>
                <button type="submit" class="btn-login" id="btn-submit">Masuk</button>
            </form>
        </div>
    </body>

</html>
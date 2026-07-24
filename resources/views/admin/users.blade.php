@extends('layouts.admin')

@section('title', 'Pengguna')

@section('content')
<div class="admin-page-heading">
    <div>
        <span class="admin-eyebrow">PENGATURAN</span>
        <h1>Pengguna Admin</h1>
        <p>Kelola staf admin yang memiliki akses ke sistem.</p>
    </div>
</div>

<article class="admin-panel admin-table-panel">
    <div class="admin-panel__head">
        <div><h2>Daftar Staf Admin</h2></div>
        <button class="admin-button admin-button--primary" onclick="document.getElementById('modal-add-user').style.display='flex'">Tambah Staf</button>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Waktu Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="users-tbody">
                <tr><td colspan="3"><div class="admin-table-empty"><strong>Memuat data...</strong></div></td></tr>
            </tbody>
        </table>
    </div>
</article>

<div id="modal-add-user" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:100; align-items:center; justify-content:center;">
    <div class="admin-panel" style="width:420px;">
        <div class="admin-panel__head"><h2>Tambah Staf Admin</h2></div>
        <form id="form-add-user">
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">ID (opsional)</label>
                <input type="number" id="user-id" class="admin-select" style="width:100%; padding:8px 10px; border-radius:6px;">
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Nama Lengkap</label>
                <input type="text" id="user-name" class="admin-select" style="width:100%; padding:8px 10px; border-radius:6px;" required>
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Password</label>
                <input type="password" id="user-password" class="admin-select" style="width:100%; padding:8px 10px; border-radius:6px;" required>
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" class="admin-button admin-button--secondary" onclick="document.getElementById('modal-add-user').style.display='none'">Batal</button>
                <button type="submit" class="admin-button admin-button--primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Password Akses Rahasia (Easter Egg) -->
<div id="modal-easter-egg-lock" style="display:none; position:fixed; inset:0; background:rgba(17,19,24,0.92); z-index:999; align-items:center; justify-content:center; backdrop-filter:blur(4px);">
    <div class="admin-panel" style="width:380px; text-align:center; padding:28px 24px; border:1px solid #374151; box-shadow:0 20px 25px -5px rgba(0,0,0,0.5);">
        <div style="width:54px; height:54px; background:#eff6ff; color:#2563eb; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
            <svg viewBox="0 0 24 24" style="width:28px; height:28px; fill:none; stroke:currentColor; stroke-width:2; stroke-linecap:round; stroke-linejoin:round;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        </div>
        <h2 style="font-size:18px; font-weight:700; color:#111318; margin-bottom:6px;">Akses Rahasia Staf Admin</h2>
        <p style="font-size:13px; color:#6d727c; margin-bottom:20px; line-height:1.4;">
            Halaman ini dilindungi password khusus. Masukkan password rahasia untuk membuka akses pembuatan akun admin.
        </p>
        <form id="form-easter-egg-auth">
            <div style="margin-bottom:16px;">
                <input type="password" id="easter-egg-password" class="admin-select" style="width:100%; padding:10px 12px; font-size:14px; text-align:center; border-radius:6px; letter-spacing:2px;" placeholder="Masukkan password..." required>
                <div id="easter-egg-error" style="display:none; color:#ba1b2b; font-size:12px; margin-top:8px; font-weight:600;">Password rahasia salah!</div>
            </div>
            <div style="display:flex; gap:10px; justify-content:center;">
                <a href="/admin/login" class="admin-button admin-button--secondary" style="text-decoration:none; display:inline-flex; align-items:center;">Ke Login</a>
                <button type="submit" class="admin-button admin-button--primary">Buka Akses</button>
            </div>
        </form>
    </div>
</div>
@endsection

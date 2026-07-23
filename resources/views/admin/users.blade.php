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
        <div>
            <h2>Daftar Staf Admin</h2>
        </div>
        <button class="admin-button admin-button--primary" onclick="document.getElementById('modal-add-user').style.display='flex'">Tambah Staf</button>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Waktu Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="users-tbody">
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

<!-- Modal Tambah Staf -->
<div id="modal-add-user" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center;">
    <div class="admin-panel" style="width: 400px;">
        <div class="admin-panel__head">
            <h2>Tambah Staf Admin</h2>
        </div>
        <form id="form-add-user">
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-size:12px; font-weight:bold; margin-bottom:5px;">ID (Angka acak jika kosong)</label>
                <input type="number" id="user-id" class="admin-select" style="width: 100%; padding: 5px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-size:12px; font-weight:bold; margin-bottom:5px;">Nama Lengkap</label>
                <input type="text" id="user-name" class="admin-select" style="width: 100%; padding: 5px;" required>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-size:12px; font-weight:bold; margin-bottom:5px;">Email</label>
                <input type="email" id="user-email" class="admin-select" style="width: 100%; padding: 5px;" required>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-size:12px; font-weight:bold; margin-bottom:5px;">Password</label>
                <input type="password" id="user-password" class="admin-select" style="width: 100%; padding: 5px;" required>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="admin-button admin-button--secondary" onclick="document.getElementById('modal-add-user').style.display='none'">Batal</button>
                <button type="submit" class="admin-button admin-button--primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

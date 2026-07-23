@extends('layouts.admin')

@section('title', 'Transaksi Masuk')

@section('content')
<div class="admin-page-heading">
    <div>
        <span class="admin-eyebrow">OPERASIONAL</span>
        <h1>Transaksi Masuk</h1>
        <p>Kelola dan perbarui status pesanan dari pelanggan.</p>
    </div>
</div>

<article class="admin-panel admin-table-panel">
    <div class="admin-panel__head">
        <div>
            <h2>Daftar Pesanan</h2>
        </div>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pelanggan</th>
                    <th>Kota</th>
                    <th>Waktu</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="orders-tbody">
                <tr>
                    <td colspan="6">
                        <div class="admin-table-empty">
                            <strong>Memuat data...</strong>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</article>

<!-- Modal Update Status -->
<div id="modal-update-order" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center;">
    <div class="admin-panel" style="width: 400px;">
        <div class="admin-panel__head">
            <h2>Update Status Pesanan</h2>
        </div>
        <form id="form-update-order">
            <input type="hidden" id="update-order-id">
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-size:12px; font-weight:bold; margin-bottom:5px;">Status Baru</label>
                <select id="update-order-status" class="admin-select" style="width: 100%; padding: 5px;">
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="admin-button admin-button--secondary" onclick="document.getElementById('modal-update-order').style.display='none'">Batal</button>
                <button type="submit" class="admin-button admin-button--primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

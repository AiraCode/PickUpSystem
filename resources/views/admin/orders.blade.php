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
        <div><h2>Daftar Pesanan</h2></div>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Pelanggan</th>
                    <th>Kota</th>
                    <th>Alamat Jemput</th>
                    <th>Waktu</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="orders-tbody">
                <tr><td colspan="6"><div class="admin-table-empty"><strong>Memuat data...</strong></div></td></tr>
            </tbody>
        </table>
    </div>
</article>

<div id="modal-update-order" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:100; align-items:center; justify-content:center;">
    <div class="admin-panel" style="width:440px;">
        <div class="admin-panel__head"><h2>Update Status Pesanan</h2></div>
        <form id="form-update-order">
            <input type="hidden" id="update-order-id">
            <div style="margin-bottom:18px;">
                <label style="display:block; font-size:12px; font-weight:600; margin-bottom:10px; color:#4a4f59;">Pilih Status Baru</label>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <label style="display:flex; align-items:center; gap:10px; padding:10px 14px; border:2px solid #e5e7eb; border-radius:8px; cursor:pointer; transition:border-color .15s;" onmouseover="this.style.borderColor='#3b82f6'" onmouseout="if(!this.querySelector('input').checked) this.style.borderColor='#e5e7eb'">
                        <input type="radio" name="order_status" value="pending" style="accent-color:#f59e0b; width:16px; height:16px;">
                        <div>
                            <span style="font-weight:600; font-size:13px; color:#111318;">Pending</span>
                            <span style="display:block; font-size:11px; color:#6d727c;">Pesanan masuk, menunggu diproses</span>
                        </div>
                    </label>
                    <label style="display:flex; align-items:center; gap:10px; padding:10px 14px; border:2px solid #e5e7eb; border-radius:8px; cursor:pointer; transition:border-color .15s;" onmouseover="this.style.borderColor='#3b82f6'" onmouseout="if(!this.querySelector('input').checked) this.style.borderColor='#e5e7eb'">
                        <input type="radio" name="order_status" value="processing" style="accent-color:#3b82f6; width:16px; height:16px;">
                        <div>
                            <span style="font-weight:600; font-size:13px; color:#111318;">Processing</span>
                            <span style="display:block; font-size:11px; color:#6d727c;">Sedang dijemput / diproses admin</span>
                        </div>
                    </label>
                    <label style="display:flex; align-items:center; gap:10px; padding:10px 14px; border:2px solid #e5e7eb; border-radius:8px; cursor:pointer; transition:border-color .15s;" onmouseover="this.style.borderColor='#3b82f6'" onmouseout="if(!this.querySelector('input').checked) this.style.borderColor='#e5e7eb'">
                        <input type="radio" name="order_status" value="completed" style="accent-color:#10b981; width:16px; height:16px;">
                        <div>
                            <span style="font-weight:600; font-size:13px; color:#111318;">Completed</span>
                            <span style="display:block; font-size:11px; color:#6d727c;">Pesanan selesai, aki sudah diterima</span>
                        </div>
                    </label>
                    <label style="display:flex; align-items:center; gap:10px; padding:10px 14px; border:2px solid #e5e7eb; border-radius:8px; cursor:pointer; transition:border-color .15s;" onmouseover="this.style.borderColor='#3b82f6'" onmouseout="if(!this.querySelector('input').checked) this.style.borderColor='#e5e7eb'">
                        <input type="radio" name="order_status" value="cancelled" style="accent-color:#ef4444; width:16px; height:16px;">
                        <div>
                            <span style="font-weight:600; font-size:13px; color:#111318;">Cancelled</span>
                            <span style="display:block; font-size:11px; color:#6d727c;">Pesanan dibatalkan</span>
                        </div>
                    </label>
                </div>
            </div>
            <div style="margin-bottom:18px;">
                <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:#4a4f59;">Bukti Pembayaran (opsional)</label>
                <div id="upload-area" style="border:2px dashed #d1d5db; border-radius:8px; padding:20px; text-align:center; cursor:pointer; transition:border-color .15s;" onmouseover="this.style.borderColor='#3b82f6'" onmouseout="this.style.borderColor='#d1d5db'">
                    <input type="file" id="upload-proof" accept="image/*" style="display:none;">
                    <div id="upload-placeholder">
                        <svg viewBox="0 0 24 24" style="width:32px; height:32px; fill:none; stroke:#9ca3af; stroke-width:1.5; margin:0 auto 8px;"><path d="M12 16V4m0 0L8 8m4-4 4 4"/><path d="M20 16v2a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2"/></svg>
                        <p style="font-size:12px; color:#6d727c; margin:0;">Klik untuk upload atau seret file ke sini</p>
                    </div>
                    <img id="upload-preview" style="display:none; max-width:100%; max-height:150px; border-radius:6px; margin:0 auto;">
                </div>
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" class="admin-button admin-button--secondary" onclick="document.getElementById('modal-update-order').style.display='none'">Batal</button>
                <button type="submit" class="admin-button admin-button--primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="modal-detail-order" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:100; align-items:center; justify-content:center;">
    <div class="admin-panel" style="width:520px; max-height:90vh; overflow-y:auto;">
        <div class="admin-panel__head"><h2>Detail Transaksi</h2></div>
        <div style="padding-bottom:20px;">
            <div style="margin-bottom:18px; border-bottom:1px solid #e5e7eb; padding-bottom:14px;">
                <h3 style="font-size:14px; margin-bottom:12px; color:#111318;">Informasi Pelanggan</h3>
                <table style="width:100%; font-size:13px; border-collapse:collapse;">
                    <tr><td style="width:40%; color:#6d727c; padding:6px 0;">Nama Lengkap</td><td id="detail-customer-name" style="font-weight:500;"></td></tr>
                    <tr><td style="color:#6d727c; padding:6px 0;">Nomor HP</td><td id="detail-customer-phone" style="font-weight:500;"></td></tr>
                    <tr><td style="color:#6d727c; padding:6px 0;">Alamat</td><td id="detail-customer-address" style="font-weight:500;"></td></tr>
                    <tr><td style="color:#6d727c; padding:6px 0;">No. KTP</td><td id="detail-customer-ktp" style="font-weight:500;"></td></tr>
                    <tr><td style="color:#6d727c; padding:6px 0;">Bank & Rekening</td><td id="detail-customer-bank" style="font-weight:500;"></td></tr>
                </table>
            </div>
            <div>
                <h3 style="font-size:14px; margin-bottom:12px; color:#111318;">Informasi Pesanan</h3>
                <table style="width:100%; font-size:13px; border-collapse:collapse;">
                    <tr><td style="width:40%; color:#6d727c; padding:6px 0;">Kota Layanan</td><td id="detail-order-city" style="font-weight:500;"></td></tr>
                    <tr><td style="color:#6d727c; padding:6px 0;">Status</td><td id="detail-order-status" style="font-weight:500;"></td></tr>
                    <tr><td style="color:#6d727c; padding:6px 0;">Waktu Pesan</td><td id="detail-order-time" style="font-weight:500;"></td></tr>
                    <tr><td style="color:#6d727c; padding:6px 0;">Alamat Penjemputan</td><td id="detail-order-pickup-address" style="font-weight:500;"></td></tr>
                    <tr><td style="color:#6d727c; padding:6px 0;">Catatan Alamat</td><td id="detail-order-pickup-note" style="font-weight:500;"></td></tr>
                </table>
            </div>
        </div>
        <div style="display:flex; justify-content:flex-end; padding-top:10px; border-top:1px solid #e5e7eb;">
            <button type="button" class="admin-button admin-button--primary" onclick="document.getElementById('modal-detail-order').style.display='none'">Tutup</button>
        </div>
    </div>
</div>
@endsection

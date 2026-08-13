<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="UTF-8">
        <title>Struk Tanda Terima Pengambilan Aki - {{ $warehouse->name }}</title>
        <style>
            @page {
                size: A4 portrait;
                margin: 20px 25px 25px 25px;
            }

            body {
                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                font-size: 11px;
                color: #1e293b;
                line-height: 1.4;
                background-color: #ffffff;
            }

            .header-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
                border-bottom: 2px solid #16a34a;
                padding-bottom: 12px;
            }

            .header-table td {
                vertical-align: middle;
            }

            .company-logo {
                max-height: 55px;
                width: auto;
            }

            .doc-title {
                text-align: right;
            }

            .doc-title h2 {
                margin: 0;
                font-size: 18px;
                font-weight: 800;
                color: #14532d;
                letter-spacing: 0.5px;
                text-transform: uppercase;
            }

            .doc-title p {
                margin: 2px 0 0 0;
                font-size: 10px;
                color: #64748b;
                font-weight: 500;
            }

            .status-badge {
                display: inline-block;
                background-color: #dcfce7;
                color: #15803d;
                border: 1px solid #bbf7d0;
                padding: 3px 10px;
                border-radius: 12px;
                font-size: 9px;
                font-weight: 700;
                text-transform: uppercase;
                margin-top: 4px;
            }

            .meta-card {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
                background-color: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 6px;
            }

            .meta-card td {
                padding: 10px 14px;
                width: 50%;
                vertical-align: top;
            }

            .meta-item {
                margin-bottom: 6px;
            }

            .meta-item:last-child {
                margin-bottom: 0;
            }

            .meta-label {
                font-size: 9px;
                font-weight: 700;
                color: #64748b;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .meta-value {
                font-size: 11px;
                font-weight: 600;
                color: #0f172a;
                margin-top: 1px;
            }

            .items-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }

            .items-table th {
                background-color: #15803d;
                color: #ffffff;
                font-size: 10px;
                font-weight: 700;
                text-transform: uppercase;
                padding: 8px 10px;
                text-align: left;
                border: 1px solid #15803d;
            }

            .items-table td {
                padding: 8px 10px;
                border: 1px solid #cbd5e1;
                font-size: 10.5px;
            }

            .items-table tbody tr:nth-child(even) {
                background-color: #f8fafc;
            }

            .text-center {
                text-align: center;
            }

            .text-right {
                text-align: right;
            }

            .grand-total-row td {
                background-color: #f0fdf4;
                border-top: 2px solid #16a34a;
                font-weight: 800;
                color: #14532d;
                font-size: 11px;
            }

            .notice-box {
                background-color: #f0fdf4;
                border-left: 4px solid #16a34a;
                border-top: 1px solid #bbf7d0;
                border-right: 1px solid #bbf7d0;
                border-bottom: 1px solid #bbf7d0;
                padding: 10px 12px;
                border-radius: 4px;
                margin-bottom: 25px;
            }

            .notice-box p {
                margin: 0;
                font-size: 9.5px;
                color: #14532d;
            }

            .signatures-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 30px;
            }

            .signatures-table td {
                width: 50%;
                text-align: center;
                vertical-align: top;
            }

            .sig-title {
                font-size: 10px;
                font-weight: 700;
                color: #475569;
                text-transform: uppercase;
                margin-bottom: 50px;
            }

            .sig-name {
                font-size: 11px;
                font-weight: 700;
                color: #0f172a;
                border-bottom: 1px dashed #94a3b8;
                display: inline-block;
                padding-bottom: 2px;
                min-width: 160px;
            }

            .sig-role {
                font-size: 9.5px;
                color: #64748b;
                margin-top: 3px;
            }

            .footer-note {
                margin-top: 35px;
                text-align: center;
                font-size: 8.5px;
                color: #94a3b8;
                border-top: 1px solid #f1f5f9;
                padding-top: 8px;
            }
        </style>
    </head>

    <body>

        <!-- Header Section -->
        <table class="header-table">
            <tr>
                <td style="width: 45%;">
                    <h1>MODERN MULYA MANDIRI</h1>
                </td>
                <td class="doc-title" style="width: 55%;">
                    <h2>STRUK TANDA TERIMA PENGAMBILAN AKI</h2>
                    <p>No. Tanda Terima: REC-WH-{{ $warehouse->id }}-{{ date('YmdHi') }}</p>
                </td>
            </tr>
        </table>

        <!-- Meta Information Cards -->
        <table class="meta-card">
            <tr>
                <td>
                    <div class="meta-item">
                        <div class="meta-label">Nama Gudang Cabang</div>
                        <div class="meta-value">{{ $warehouse->name }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Lokasi / Gudang</div>
                        <div class="meta-value">
                            {{ isset($warehouse->city) && $warehouse->city ? $warehouse->city->name : 'Gudang Cabang ' . $warehouse->name }}
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Alamat Lengkap Gudang</div>
                        <div class="meta-value" style="font-weight:400; font-size:10px;">
                            {{ $warehouse->address ?: '-' }}
                        </div>
                    </div>
                </td>
                <td style="border-left: 1px solid #e2e8f0;">
                    <div class="meta-item">
                        <div class="meta-label">Waktu Konfirmasi Pengambilan</div>
                        <div class="meta-value">{{ $receiptDate }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Petugas Admin Gudang</div>
                        <div class="meta-value">{{ $adminName }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Total Unit Diterima Pusat</div>
                        <div class="meta-value" style="color:#15803d; font-size:12px;">
                            {{ number_format($totalQty, 0, ',', '.') }} Unit
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Rincian Stok Table -->
        <h4 style="margin: 0 0 8px 0; color:#14532d; font-size:12px; font-weight:700; text-transform:uppercase;">
            Rincian Aki Yang Diambil Oleh Pusat
        </h4>

        <table class="items-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 35px;">NO</th>
                    <th>JENIS AKI (BATTERY NAME)</th>
                    <th class="text-right">BERAT KERING / UNIT (KG)</th>
                    <th class="text-center">JUMLAH (QTY)</th>
                    <th class="text-right">SUBTOTAL BERAT (KG)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $index => $item)
                    @php
                        $unitWeight = (float) ($item->berat_kering ?? 0);
                        $qty = (int) ($item->total_qty ?? 0);
                        $subtotalWeight = $unitWeight * $qty;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td style="font-weight:600;">{{ $item->name }}</td>
                        <td>{{ $item->brand ?? '-' }}</td>
                        <td class="text-right">{{ number_format($unitWeight, 2, ',', '.') }} kg</td>
                        <td class="text-center" style="font-weight:700; color:#15803d;">
                            {{ number_format($qty, 0, ',', '.') }} Pcs
                        </td>
                        <td class="text-right" style="font-weight:600;">{{ number_format($subtotalWeight, 2, ',', '.') }} kg
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center" style="padding:15px; color:#64748b;">
                            Tidak ada rincian data stok aki.
                        </td>
                    </tr>
                @endforelse

                <tr class="grand-total-row">
                    <td colspan="4" class="text-right">GRAND TOTAL AKI DITERIMA PUSAT:</td>
                    <td class="text-center" style="font-size:12px;">{{ number_format($totalQty, 0, ',', '.') }} Pcs</td>
                    <td class="text-right" style="font-size:12px;">{{ number_format($totalWeight, 2, ',', '.') }} kg
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Notice Box -->
        <div class="notice-box">
            <p><strong>Tanda Terima Resmi:</strong> Dokumen ini merupakan bukti sah pengambilan barang oleh Tim Pusat
                dari Gudang {{ $warehouse->name }}. Seluruh unit aki terlampir telah diverifikasi dan diserahkan secara
                lengkap.</p>
        </div>

        <!-- Signatures -->
        <table class="signatures-table">
            <tr>
                <td>
                    <div class="sig-title">Yang Menyerahkan (Admin Gudang)</div>
                    <div class="sig-name">{{ $adminName }}</div>
                    <div class="sig-role">Gudang {{ $warehouse->name }}</div>
                </td>
                <td>
                    <div class="sig-title">Yang Menerima (Admin Pusat)</div>
                    <div class="sig-name">Tim Operasional Pusat</div>
                    <div class="sig-role">Akiku System Central</div>
                </td>
            </tr>
        </table>

        <div class="footer-note">
            Struk Resmi Tanda Terima Pengambilan Aki &bull; Printed via AKIKU &bull; {{ date('d/m/Y H:i:s') }}
            WIB
        </div>

    </body>

</html>
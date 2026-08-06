<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesanan Baru Diterima</title>
</head>
<body>
    <h1>Pesanan Baru Diterima</h1>
    <p>Pesanan baru telah dibuat dan memerlukan perhatian:</p>

    <h2>Ringkasan Pesanan</h2>
    <ul>
        <li><strong>ID Pesanan:</strong> #{{ $order->id }}</li>
        <li><strong>Tipe Pesanan:</strong> {{ $order->order_type === 'trade_in' ? 'Tukar Tambah Aki' : 'Penjualan Aki Bekas' }}</li>
        <li><strong>Metode Pengantaran:</strong> {{ ucfirst($order->delivery_method) }}</li>
        <li><strong>Status:</strong> {{ ucfirst($order->status) }}</li>
    </ul>

    <h2>Detail Pelanggan</h2>
    <ul>
        <li><strong>Nama:</strong> {{ $customer->name }}</li>
        <li><strong>Telepon:</strong> {{ $customer->phone_number }}</li>
        <li><strong>Alamat:</strong> {{ $customer->address }}</li>
        <li><strong>Note Alamat:</strong> {{ $order->pickup_address_note ?? '-' }}</li>
    </ul>

    <h2>Detail Pickup</h2>
    <ul>
        <li><strong>Kota:</strong> {{ $order->city?->name ?? 'N/A' }}</li>
        <li><strong>Alamat Pickup:</strong> {{ $order->pickup_address }}</li>
    </ul>

    <h2>Biaya</h2>
    <p>Jumlah total pesanan: Rp {{ number_format(abs($totalCost), 0, ',', '.') }} {{ $totalCost < 0 ? '(Harus dibayar)' : '(Kelebihan dana)' }}</p>

    <p>Silakan cek detail pesanan di sistem admin untuk informasi lanjutan.</p>
</body>
</html>

# Walkthrough — Admin Backend API PickUp System

Semua file sudah selesai dibuat dan diverifikasi. Berikut penjelasan lengkap setiap file beserta fungsinya.

---

## 📦 Models (app/Models/) — "Peta" Database

Model adalah representasi setiap tabel database di dalam kode Laravel. Setiap model mendefinisikan kolom apa saja yang boleh diisi dan bagaimana relasinya dengan tabel lain.

| File | Tabel | Fungsi |
|------|-------|--------|
| [City.php](file:///c:/laragon/www/PickUpSystem/app/Models/City.php) | `cities` | Data kota (Jakarta, Surabaya, dll). Terhubung ke accu melalui harga per kota, dan punya banyak order. |
| [Accu.php](file:///c:/laragon/www/PickUpSystem/app/Models/Accu.php) | `accus` | Jenis accu (brand + nama). Terhubung ke kota (harga berbeda per kota) dan ke receipt (accu apa yang dijual). |
| [User.php](file:///c:/laragon/www/PickUpSystem/app/Models/User.php) | `users` | Admin Indoprima. Bisa login via Sanctum token, membuat receipt, dan memproses transfer. |
| [Bank.php](file:///c:/laragon/www/PickUpSystem/app/Models/Bank.php) | `banks` | Data bank (BCA, Mandiri, dll). Customer memilih bank untuk menerima pembayaran. |
| [Customer.php](file:///c:/laragon/www/PickUpSystem/app/Models/Customer.php) | `customers` | Data penjual accu (nama, alamat, KTP, rekening, dll). Tidak perlu login. |
| [Order.php](file:///c:/laragon/www/PickUpSystem/app/Models/Order.php) | `orders` | Pesanan penjualan accu dari customer. Berisi lokasi pickup dan status pesanan. |
| [Receipt.php](file:///c:/laragon/www/PickUpSystem/app/Models/Receipt.php) | `receipts` | Struk/bukti transaksi. Menghubungkan order dengan admin, accu yang dibeli, shipment, dan transfer. |
| [Warehouse.php](file:///c:/laragon/www/PickUpSystem/app/Models/Warehouse.php) | `storages` | Gudang penyimpanan. Nama class `Warehouse` (bukan `Storage`) untuk menghindari konflik dengan Laravel. |
| [Shipment.php](file:///c:/laragon/www/PickUpSystem/app/Models/Shipment.php) | `shipments` | Data pengiriman accu ke gudang. Mencatat tanggal pickup dan tanggal diterima. |
| [Transfer.php](file:///c:/laragon/www/PickUpSystem/app/Models/Transfer.php) | `transfers` | Data pembayaran/transfer ke customer setelah accu diterima. |

---

## 🎮 Controllers (app/Http/Controllers/) — "Otak" API

Controller menangani logika bisnis. Ketika frontend mengirim request ke URL tertentu, controller yang akan memproses dan mengembalikan response JSON.

### [AuthController.php](file:///c:/laragon/www/PickUpSystem/app/Http/Controllers/Api/AuthController.php)
**Menangani: Login & Logout Admin**
- `POST /api/login` → Admin kirim `name` + `password`, mendapat token untuk akses API
- `POST /api/logout` → Menghapus token, admin keluar dari sistem

### [CityController.php](file:///c:/laragon/www/PickUpSystem/app/Http/Controllers/Api/Admin/CityController.php)
**Menangani: CRUD Kota**
- `GET /api/admin/cities` → Lihat semua kota
- `POST /api/admin/cities` → Tambah kota baru
- `GET /api/admin/cities/{id}` → Lihat detail 1 kota
- `PUT /api/admin/cities/{id}` → Edit nama kota
- `DELETE /api/admin/cities/{id}` → Hapus kota

### [AccuController.php](file:///c:/laragon/www/PickUpSystem/app/Http/Controllers/Api/Admin/AccuController.php)
**Menangani: CRUD Jenis Accu**
- `GET /api/admin/accus` → Lihat semua jenis accu
- `POST /api/admin/accus` → Tambah jenis accu baru (brand + nama)
- `GET /api/admin/accus/{id}` → Lihat detail accu **beserta harga di setiap kota**
- `PUT /api/admin/accus/{id}` → Edit brand/nama accu
- `DELETE /api/admin/accus/{id}` → Hapus jenis accu

### [CityAccuPriceController.php](file:///c:/laragon/www/PickUpSystem/app/Http/Controllers/Api/Admin/CityAccuPriceController.php)
**Menangani: Harga Accu Per Kota**
Ini adalah inti dari fitur "harga berbeda per daerah":
- `GET /api/admin/cities/{cityId}/accus` → Lihat semua harga accu di kota tertentu
- `POST /api/admin/cities/{cityId}/accus` → Set harga accu untuk kota tersebut
- `PUT /api/admin/cities/{cityId}/accus/{accuId}` → Update harga accu di kota tersebut
- `DELETE /api/admin/cities/{cityId}/accus/{accuId}` → Hapus harga accu dari kota

### [BankController.php](file:///c:/laragon/www/PickUpSystem/app/Http/Controllers/Api/Admin/BankController.php)
**Menangani: CRUD Bank**
- `GET /api/admin/banks` → Lihat semua bank
- `POST /api/admin/banks` → Tambah bank baru
- `PUT /api/admin/banks/{id}` → Edit nama bank
- `DELETE /api/admin/banks/{id}` → Hapus bank

### [ReceiptController.php](file:///c:/laragon/www/PickUpSystem/app/Http/Controllers/Api/Admin/ReceiptController.php)
**Menangani: Melihat Struk (Read-Only)**
- `GET /api/admin/receipts` → Lihat semua struk (bisa filter berdasarkan status)
- `GET /api/admin/receipts/{id}` → Lihat detail struk lengkap (termasuk data customer, order, accu, shipment, transfer)

---

## 📝 Form Requests (app/Http/Requests/Admin/) — "Satpam" Validasi

Form Request memvalidasi data yang masuk sebelum sampai ke controller. Kalau data tidak valid, langsung ditolak dengan pesan error.

| File | Memvalidasi |
|------|-------------|
| [StoreCityRequest.php](file:///c:/laragon/www/PickUpSystem/app/Http/Requests/Admin/StoreCityRequest.php) | Tambah kota: `id` (wajib, unik), `name` (wajib, maks 45 karakter) |
| [StoreAccuRequest.php](file:///c:/laragon/www/PickUpSystem/app/Http/Requests/Admin/StoreAccuRequest.php) | Tambah accu: `id` (wajib, unik), `brand` + `name` (wajib, maks 45 karakter) |
| [UpdateAccuRequest.php](file:///c:/laragon/www/PickUpSystem/app/Http/Requests/Admin/UpdateAccuRequest.php) | Edit accu: `brand` + `name` (opsional, boleh kirim salah satu saja) |
| [StoreCityAccuPriceRequest.php](file:///c:/laragon/www/PickUpSystem/app/Http/Requests/Admin/StoreCityAccuPriceRequest.php) | Set harga: `accus_id` (wajib, harus ada di tabel accus), `price` (wajib, minimal 0) |
| [StoreBankRequest.php](file:///c:/laragon/www/PickUpSystem/app/Http/Requests/Admin/StoreBankRequest.php) | Tambah bank: `id` (wajib, unik), `name` (wajib, maks 45 karakter) |

---

## 🛤️ Routes (routes/api.php) — "Peta Jalan" API

[api.php](file:///c:/laragon/www/PickUpSystem/routes/api.php) mendaftarkan semua URL API dan menghubungkannya ke controller yang tepat.

- **Public** (tanpa login): `POST /api/login`
- **Protected** (harus login dulu): semua endpoint di bawah `/api/admin/*`

---

## ⚙️ Config

[bootstrap/app.php](file:///c:/laragon/www/PickUpSystem/bootstrap/app.php) — Ditambahkan baris `api:` agar Laravel membaca file `routes/api.php`.

---

## ✅ Verification Results

| Check | Status |
|-------|--------|
| `php artisan route:list` | ✅ 22 routes terdaftar |
| `php artisan migrate:fresh --seed` | ✅ 5 migrations + seeder berhasil |
| Sanctum installed | ✅ `personal_access_tokens` table created |

---

## 🧪 Cara Test API

### 1. Login (dapatkan token)
```
POST http://localhost/PickUpSystem/public/api/login
Body (JSON): { "name": "Admin Test", "password": "password123" }
```

### 2. Gunakan token untuk akses admin
Tambahkan header di setiap request:
```
Authorization: Bearer <token_dari_login>
```

### 3. Contoh: Tambah accu baru
```
POST http://localhost/PickUpSystem/public/api/admin/accus
Headers: Authorization: Bearer <token>
Body (JSON): { "id": 3, "brand": "Incoe", "name": "NS70" }
```

### 4. Contoh: Set harga accu di Jakarta
```
POST http://localhost/PickUpSystem/public/api/admin/cities/1/accus
Headers: Authorization: Bearer <token>
Body (JSON): { "accus_id": 3, "price": 200000 }
```

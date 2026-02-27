# Implementasi Fitur Stock Out Distributor ke Retail & Aktivasi Warranty

## Perubahan yang Dilakukan

### 1. Hapus Fitur Sales
- Tabel `sales` telah dihapus
- Model `Sale` tidak lagi digunakan
- Route dan controller sales telah dihapus

### 2. Tambah Kolom Stock Management
- Kolom `at_distributor` ditambahkan ke tabel `products` untuk tracking stok di distributor
- Kolom `at_retail` ditambahkan ke tabel `products` untuk tracking stok di retail

### 3. Fitur Stock Out Distributor ke Retail
**URL:** `/distributor/stock-out`

**Fitur:**
- Scan barcode produk menggunakan camera scanner atau input manual
- Validasi stok di distributor
- Checkout barang dari distributor ke retail
- Otomatis update `at_distributor` (berkurang) dan `at_retail` (bertambah)
- Logging ke `product_trace_logs` dengan event_type `stock_out_to_retail`

**Controller:** `DistributorStockOutController`
- `index()` - Tampilkan halaman stock out
- `scan()` - Scan dan validasi produk
- `checkout()` - Proses checkout ke retail

### 4. Fitur Aktivasi Warranty
**URL:** `/warranty/activation`

**Fitur:**
- Input serial number produk
- Aktivasi warranty dengan mengisi `warranty_expires_at`
- Periode warranty diambil dari setting project (`warranty_period`)
- Validasi: warranty hanya bisa diaktifkan sekali

**Controller:** `WarrantyActivationController`
- `index()` - Tampilkan halaman aktivasi
- `activate()` - Proses aktivasi warranty

### 5. Update Dashboard
- Tambah card "At Retail" untuk menampilkan jumlah stok di retail
- Grid dashboard diubah dari 4 kolom menjadi 5 kolom
- Statistik menggunakan sum dari kolom `at_distributor` dan `at_retail`

## Cara Penggunaan

### Stock Out Distributor ke Retail
1. Login sebagai distributor
2. Akses menu "Stock Out" atau URL `/distributor/stock-out`
3. Scan barcode produk atau ketik serial number
4. Sistem akan menampilkan informasi produk dan stok di distributor
5. Masukkan jumlah yang akan di-checkout
6. Klik "Checkout ke Retail"
7. Stok akan berpindah dari distributor ke retail

### Aktivasi Warranty (Retail)
1. Akses URL `/warranty/activation`
2. Masukkan serial number produk yang dijual
3. Klik "Aktivasi"
4. Sistem akan mengisi `warranty_expires_at` berdasarkan setting project
5. Warranty hanya bisa diaktifkan sekali per produk

## Database Changes

### Migration: `2026_02_23_043146_remove_sales_and_add_retail_stock`
```sql
-- Drop sales table
DROP TABLE IF EXISTS sales;

-- Add columns to products
ALTER TABLE products 
ADD COLUMN at_distributor INT DEFAULT 0 AFTER warranty_expires_at,
ADD COLUMN at_retail INT DEFAULT 0 AFTER at_distributor;
```

## Routes Baru

```php
// Distributor Stock Out
Route::get('distributor/stock-out', [DistributorStockOutController::class, 'index']);
Route::post('distributor/stock-out/scan', [DistributorStockOutController::class, 'scan']);
Route::post('distributor/stock-out/checkout', [DistributorStockOutController::class, 'checkout']);

// Warranty Activation
Route::get('warranty/activation', [WarrantyActivationController::class, 'index']);
Route::post('warranty/activate', [WarrantyActivationController::class, 'activate']);
```

## Model Updates

### Product Model
- Tambah `at_distributor` dan `at_retail` ke `$fillable`
- Hapus relationship `sale()`

## Notes
- Fitur sales telah dihapus sepenuhnya
- Stock management sekarang menggunakan kolom `at_distributor` dan `at_retail`
- Warranty activation dilakukan oleh retail saat penjualan ke end user
- Semua perubahan stock dicatat di `product_trace_logs`

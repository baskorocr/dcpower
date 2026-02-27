# Product Trace & Retail Tracking Implementation

## Tanggal: 26 Februari 2026

## Masalah yang Diselesaikan

1. **N+1 Query di Dashboard**: Sudah ada eager loading `with(['product', 'creator'])` di DashboardController
2. **Tracking Retail**: Menambahkan kemampuan untuk tracking produk dari manufacture → distributor → retail
3. **Menampilkan Nama Retail**: Menampilkan nama retail pada halaman show warranty claim

## Perubahan yang Dilakukan

### 1. Database Migration
- **File**: `2026_02_26_001428_add_retail_id_to_stock_movements_table.php`
- **Perubahan**: Menambahkan kolom `retail_id` (nullable) ke tabel `stock_movements`
- **Tujuan**: Untuk tracking produk yang dikirim ke retail tertentu

### 2. Model StockMovement
- **File**: `app/Models/StockMovement.php`
- **Perubahan**: 
  - Menambahkan `retail_id` ke `$fillable`
  - Menambahkan relasi `retail()` ke model Retail

### 3. Controller - WarrantyClaimController
- **File**: `app/Http/Controllers/Admin/WarrantyClaimController.php`
- **Method**: `show()`
- **Perubahan**: Menambahkan eager loading untuk menghindari N+1 query:
  ```php
  $warrantyClaim->load([
      'product.stockMovements.distributor',
      'product.stockMovements.retail',
      'histories.actor'
  ]);
  ```

### 4. Controller - StockOutController
- **File**: `app/Http/Controllers/StockOutController.php`
- **Method**: `process()`
- **Perubahan**: Menambahkan `retail_id` saat membuat stock movement untuk distributor stock out:
  ```php
  StockMovement::create([
      'product_id' => $productId,
      'distributor_id' => $distributor->id,
      'retail_id' => $validated['retail_id'] ?? null,
      'type' => 'out',
      'quantity' => $count,
      'moved_at' => now(),
  ]);
  ```

### 5. View - Warranty Claim Show
- **File**: `resources/views/admin/warranty-claims/show.blade.php`
- **Perubahan**:
  1. Menambahkan informasi retail di bagian claim info
  2. Menambahkan section "Product Trace" yang menampilkan:
     - Manufacture (dari project)
     - Distributor (dari stock_movements dengan type='in')
     - Retail (dari stock_movements dengan retail_id)

## Cara Kerja Product Trace

1. **Manufacture → Distributor**:
   - Saat admin/project melakukan stock out ke distributor
   - Dibuat record di `stock_movements` dengan:
     - `type = 'in'`
     - `distributor_id = [id distributor]`
     - `retail_id = null`

2. **Distributor → Retail**:
   - Saat distributor melakukan stock out ke retail
   - Dibuat record di `stock_movements` dengan:
     - `type = 'out'`
     - `distributor_id = [id distributor]`
     - `retail_id = [id retail]`

3. **Menampilkan Trace**:
   - Query stock_movements dengan eager loading
   - Filter berdasarkan type dan keberadaan retail_id
   - Tampilkan dalam urutan kronologis

## Testing

Untuk testing perubahan ini:

1. Pastikan migration sudah dijalankan:
   ```bash
   php artisan migrate
   ```

2. Lakukan stock out dari distributor ke retail dengan memilih retail tertentu

3. Buka halaman warranty claim detail untuk produk tersebut

4. Verifikasi:
   - Tidak ada N+1 query (cek dengan Laravel Debugbar atau query log)
   - Nama retail muncul di bagian claim info
   - Section "Product Trace" menampilkan alur lengkap: Manufacture → Distributor → Retail

## Catatan

- Untuk produk yang sudah ada sebelum update ini, retail_id akan null karena belum ada tracking
- Hanya produk yang di-stock-out setelah update ini yang akan memiliki informasi retail lengkap
- Eager loading sudah diterapkan untuk menghindari N+1 query problem

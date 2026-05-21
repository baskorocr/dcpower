# Fitur Edit Status Repair untuk Produk Warranty Expired

## Deskripsi
Fitur ini memungkinkan Admin dan Marketing untuk mengedit status repair pada produk yang warranty-nya sudah expired. Jika produk masih bisa di-repair, mereka dapat mengirimkan produk tersebut ke distributor lain untuk diperbaiki.

## Fitur Utama

### 1. Edit Status Repair
- Tombol "Edit Status" muncul pada produk dengan status `warranty_expired`
- Hanya dapat diakses oleh role: **Admin** dan **Marketing**
- Modal form untuk mengatur:
  - Apakah produk dapat di-repair (Yes/No)
  - Jika Yes, pilih distributor tujuan pengiriman
  - Catatan tambahan tentang repair

### 2. Informasi Status di Tabel
- Status produk menampilkan badge "Can Repair" jika produk bisa diperbaiki
- Menampilkan nama distributor tujuan di bawah status
- Visual yang jelas dengan warna purple untuk status repair

### 3. Tracking & Logging
- Setiap perubahan status repair dicatat di trace logs
- Mencatat user yang melakukan perubahan
- Mencatat waktu pengiriman ke distributor
- Mencatat catatan repair

## Database Schema

### Tabel: products
Kolom baru yang ditambahkan:
- `can_repair` (boolean): Apakah produk dapat di-repair
- `repair_distributor_id` (bigint, nullable): ID distributor tujuan repair
- `repair_notes` (text, nullable): Catatan tentang repair
- `repair_sent_at` (timestamp, nullable): Waktu pengiriman ke distributor

## Cara Penggunaan

### Untuk Admin/Marketing:
1. Buka halaman Products (`/products`)
2. Filter produk dengan status "Warranty Expired"
3. Klik tombol "Edit Status" pada produk yang ingin diubah
4. Pada modal yang muncul:
   - Pilih "Yes" jika produk dapat di-repair
   - Pilih distributor tujuan dari dropdown
   - Tambahkan catatan jika diperlukan
   - Klik "Save Status"
5. Status akan terupdate dan tercatat di trace logs

### Melihat History Repair:
1. Klik "View" pada produk
2. Lihat di bagian Trace Logs untuk melihat history perubahan status repair

## API Endpoint

### Update Repair Status
```
POST /products/{product}/repair-status
```

**Request Body:**
```json
{
  "can_repair": true,
  "repair_distributor_id": 1,
  "repair_notes": "Battery issue, needs replacement"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Repair status updated successfully!"
}
```

## Validasi
- `can_repair`: Required, boolean
- `repair_distributor_id`: Required jika can_repair = true, harus ada di tabel distributors
- `repair_notes`: Optional, maksimal 1000 karakter

## Permission
- Route dilindungi dengan middleware: `permission:manage-products`
- Tombol Edit Status hanya muncul untuk role: Admin dan Marketing

## Migration
File: `2026_05_20_044512_add_repair_status_to_products_table.php`

Untuk rollback:
```bash
php artisan migrate:rollback --step=1
```

## Model Relationship
```php
// Product Model
public function repairDistributor()
{
    return $this->belongsTo(Distributor::class, 'repair_distributor_id');
}
```

## Testing
1. Login sebagai Admin atau Marketing
2. Buat produk dengan status warranty_expired (atau ubah status produk existing)
3. Test edit status repair dengan berbagai skenario:
   - Set can_repair = Yes dengan distributor
   - Set can_repair = No
   - Update status yang sudah ada
4. Verifikasi data tersimpan di database
5. Verifikasi trace log tercatat dengan benar

## Notes
- Fitur ini hanya berlaku untuk produk dengan status `warranty_expired`
- Distributor yang dipilih harus memiliki status `active`
- Setiap perubahan status akan tercatat di product trace logs
- Informasi repair akan ditampilkan di halaman detail produk

# Trace Log & QR Code Fix - 13 Feb 2026

## Masalah yang Ditemukan

1. **Trace history tidak terupdate** saat stock out
2. **Error qr_code saat create product**: `Field 'qr_code' doesn't have a default value`

## Akar Masalah

### 1. Trace Log
- Stock out process tidak mencatat trace log
- Produk yang sudah di-stock out tidak memiliki history pengiriman

### 2. QR Code Error
- Migration untuk remove kolom `qr_code`, `name`, `description` sudah tercatat tapi tidak jalan
- Kolom-kolom tersebut masih ada di database
- Sistem sudah tidak menggunakan kolom `qr_code` (diganti dengan `serial_number`)

## Perbaikan yang Dilakukan

### 1. ProductTraceLog Model & Migration

**Migration baru**: `2026_02_13_142501_add_fields_to_product_trace_logs_table.php`
- Menambah kolom `scanned_by` (foreign key ke users)
- Menambah kolom `action` (string untuk action type)
- Menambah kolom `notes` (text untuk catatan detail)

**Model Update**:
```php
protected $fillable = [
    'product_id', 'user_id', 'scanned_by', 'action', 
    'event_type', 'location', 'notes', 'latitude', 
    'longitude', 'scanned_at'
];
```

### 2. StockOutController

**Tambah import**:
```php
use App\Models\ProductTraceLog;
```

**Update method `scan()`**:
- Hapus referensi `qr_code` yang sudah tidak ada
- Hanya gunakan `serial_number`

**Update method `process()`**:
- Tambah trace log saat stock out:
```php
ProductTraceLog::create([
    'product_id' => $product->id,
    'scanned_by' => $user->id,
    'action' => 'stock_out',
    'location' => $distributor->name,
    'notes' => "Shipped to distributor: {$distributor->name}",
    'scanned_at' => now(),
]);
```

### 3. ProductController (Admin)

**Update method `store()`**:
- Perbaiki trace log creation dengan field yang lengkap:
```php
$product->traceLogs()->create([
    'user_id' => auth()->id(),
    'scanned_by' => auth()->id(),
    'event_type' => 'manufactured',
    'action' => 'manufactured',
    'location' => 'Factory',
    'notes' => 'Product manufactured',
    'scanned_at' => now(),
]);
```

### 4. Database Cleanup

**Drop kolom yang tidak dipakai**:
```sql
ALTER TABLE products DROP COLUMN qr_code;
ALTER TABLE products DROP COLUMN name;
ALTER TABLE products DROP COLUMN description;
```

**Backfill trace logs** untuk stock movement yang sudah ada:
- Menambahkan trace log untuk produk yang sudah di-stock out sebelumnya
- 2 produk berhasil ditambahkan trace log-nya

## Hasil Perbaikan

### ✅ Trace Log
- Stock out sekarang mencatat trace log dengan detail:
  - Action: stock_out
  - Location: nama distributor
  - Notes: "Shipped to distributor: {nama}"
  - Scanned by: user yang melakukan stock out

### ✅ Create Product
- Error qr_code sudah tidak muncul
- Product bisa dibuat dengan sukses
- Trace log "manufactured" otomatis tercatat

### ✅ Product Detail Page
- Trace history sekarang menampilkan:
  1. Manufactured (saat product dibuat)
  2. Stock Out (saat dikirim ke distributor)

## Testing

### Test Create Product
```bash
# Via web interface
1. Login sebagai admin/QA
2. Buka /products/create
3. Scan/input serial number
4. Submit
5. Verifikasi: product created tanpa error
```

### Test Stock Out dengan Trace Log
```bash
1. Login sebagai admin/project manager
2. Buka /stock-out
3. Scan produk manufactured
4. Pilih distributor
5. Process stock out
6. Buka detail produk
7. Verifikasi: trace history menampilkan "Shipped to distributor"
```

## File yang Diubah

- `/app/Models/ProductTraceLog.php` - Update fillable
- `/app/Http/Controllers/StockOutController.php` - Tambah trace log & hapus qr_code
- `/app/Http/Controllers/Admin/ProductController.php` - Perbaiki trace log creation
- `/database/migrations/2026_02_13_142501_add_fields_to_product_trace_logs_table.php` - Migration baru
- Database: Drop kolom qr_code, name, description dari products table

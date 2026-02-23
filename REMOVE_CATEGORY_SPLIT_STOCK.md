# Remove Category & Split Stock Dashboard - 13 Feb 2026

## Perubahan yang Dilakukan

### 1. Hapus Category dari Product ✅

**Alasan**: Category tidak diperlukan dalam menambahkan product

**Perubahan**:
- ✅ Migration: Drop kolom `category_id` dan foreign key-nya
- ✅ Model Product: Hapus `category_id` dari fillable
- ✅ Model Product: Hapus relasi `category()`
- ✅ ProductController: Hapus parameter categories dari create
- ✅ ProductController: Hapus `category_id` dari store
- ✅ StockOutController: Hapus referensi `$product->category->name`

**Hasil**:
- Product bisa dibuat tanpa category
- Tidak ada error saat create product
- Stock out scan tidak lagi bergantung pada category

---

### 2. Pisahkan Stock di Dashboard ✅

**Sebelum**: 
- 1 card "Stock Available" (gabungan manufactured + in_stock)

**Sesudah**:
- 2 card terpisah:
  1. **"Manufactured"** (biru) - Stock di pabrik/warehouse (status: manufactured)
  2. **"At Distributor"** (hijau) - Stock di distributor (status: in_stock)

**Perubahan**:

#### routes/web.php - Dashboard Logic
```php
// Admin/Project Manager
'stock_manufactured' => Product::where('status', 'manufactured')->count(),
'stock_distributor' => Product::where('status', 'in_stock')->count(),

// Distributor
'stock_manufactured' => 0,
'stock_distributor' => (stock dari stock_movements),
```

#### dashboard.blade.php - UI
- Grid berubah dari 3 kolom menjadi 4 kolom
- Card 1: Stock Manufactured (biru, icon factory)
- Card 2: Stock at Distributor (hijau, icon box)
- Card 3: Total Sold (cyan)
- Card 4: Pending Claims (orange)

---

## Testing

### Test 1: Create Product Tanpa Category
```bash
✅ Product created without category!
Serial: TEST-NOCATEGORY-1770993048
```

### Test 2: Dashboard Stats
```bash
Stock Manufactured: 4
Stock at Distributor: 3
```

---

## File yang Diubah

1. **Database**:
   - `/database/migrations/2026_02_13_142857_remove_category_from_products.php`
   - Drop kolom `category_id` dari tabel `products`

2. **Models**:
   - `/app/Models/Product.php` - Hapus category_id & relasi

3. **Controllers**:
   - `/app/Http/Controllers/Admin/ProductController.php` - Hapus category
   - `/app/Http/Controllers/StockOutController.php` - Hapus referensi category

4. **Routes**:
   - `/routes/web.php` - Split stock stats

5. **Views**:
   - `/resources/views/dashboard.blade.php` - 2 card stock terpisah

---

## Dashboard Stats Breakdown

### Untuk Admin/Project Manager:
- **Manufactured**: Produk dengan status `manufactured` (belum dikirim)
- **At Distributor**: Produk dengan status `in_stock` (sudah di distributor)
- **Total Sold**: Produk dengan status `sold`
- **Pending Claims**: Warranty claims dengan status `pending`

### Untuk Distributor:
- **Manufactured**: 0 (tidak relevan)
- **At Distributor**: Dihitung dari `stock_movements` (SUM in - out)
- **Total Sold**: Sales dari distributor tersebut
- **Pending Claims**: Claims dari sales distributor tersebut

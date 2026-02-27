# Warranty Replacement Feature Implementation

## Tanggal: 26 Februari 2026

## Overview
Fitur warranty replacement memungkinkan retail untuk memproses penggantian produk untuk klaim garansi yang sudah disetujui (approved).

## Flow Proses

1. **Retail Login** → https://stg-dcpower.dharmap.com/warranty-replacement/login
   - Retail memasukkan PIN 6 digit
   - Sistem validasi dan create session

2. **List Approved Claims** → https://stg-dcpower.dharmap.com/warranty-replacement
   - Menampilkan daftar klaim yang sudah approved
   - Filter berdasarkan retail_id (dari stock_movements)
   - Hanya menampilkan klaim yang belum ada replacement_product_id

3. **Scan Replacement Product** → https://stg-dcpower.dharmap.com/warranty-replacement/{claim}
   - Retail klik "Scan Replacement" pada claim
   - Halaman scan dengan 2 opsi:
     - 📷 Camera: Scan QR code dengan kamera
     - ⌨️ Manual Input: Input serial number manual
   - Mirip dengan halaman warranty activation

4. **Process Replacement**
   - Retail scan/input serial number produk pengganti
   - Sistem validasi:
     - Product harus ada dan status: manufactured/in_distributor/at_retail
     - Product belum pernah aktivasi warranty
   - Jika valid, sistem akan:
     - **Produk Lama (yang diklaim)**: 
       - `warranty_expires_at` = now() (habiskan garansi)
       - `status` = 'warranty_expired'
     - **Produk Pengganti**:
       - `warranty_expires_at` = now() (habiskan garansi)
       - `status` = 'product_claim'
     - **Warranty Claim**:
       - `replacement_product_id` = ID produk pengganti
       - `status` = 'completed'
       - `replaced_at` = now()

## Validasi di Warranty Activation & Claim

Setelah produk digunakan untuk replacement, produk tersebut tidak bisa:

1. **Di Warranty Activation** (https://stg-dcpower.dharmap.com/warranty/activation):
   - Status 'warranty_expired' → Error: "Product warranty has expired. Cannot activate."
   - Status 'product_claim' → Error: "Product has been used for warranty claim replacement. Cannot activate."

2. **Di Warranty Claim** (https://stg-dcpower.dharmap.com/warranty-claims/create):
   - Status 'warranty_expired' → Error: "Product Warranty Expired - Garansi produk sudah habis"
   - Status 'product_claim' → Error: "Product Already Claimed - Produk sudah digunakan untuk penggantian klaim"

## File Changes

### 1. Controller - WarrantyReplacementPublicController.php
**Method `index()`:**
```php
// Filter claims berdasarkan retail_id dari stock_movements
$claims = WarrantyClaim::with(['product.stockMovements'])
    ->whereHas('product.stockMovements', function($q) use ($retailId) {
        $q->where('retail_id', $retailId);
    })
    ->where('status', 'approved')
    ->whereNull('replacement_product_id')
    ->latest()
    ->get();
```

**Method `show()`:**
```php
// Menampilkan halaman scan untuk claim tertentu
return view('warranty-replacement-scan', compact('claim'));
```

**Method `scan()`:**
```php
// Process replacement:
// 1. Expire old product warranty
$oldProduct->update([
    'warranty_expires_at' => now(),
    'status' => 'warranty_expired'
]);

// 2. Expire replacement product warranty
$replacementProduct->update([
    'warranty_expires_at' => now(),
    'status' => 'product_claim'
]);

// 3. Update claim
$claim->update([
    'replacement_product_id' => $replacementProduct->id,
    'status' => 'completed',
    'replaced_at' => now()
]);
```

### 2. View - warranty-replacement-scan.blade.php
- Halaman scan dengan QR scanner (html5-qrcode)
- Tab switching: Camera / Manual Input
- Real-time scanning dengan kamera
- Manual input sebagai fallback
- Success/error message display

### 3. Controller - WarrantyActivationController.php
**Method `activate()`:**
```php
// Validasi status produk
if ($product->status === 'warranty_expired') {
    return response()->json(['success' => false, 'message' => 'Product warranty has expired']);
}

if ($product->status === 'product_claim') {
    return response()->json(['success' => false, 'message' => 'Product has been used for warranty claim replacement']);
}
```

### 4. Controller - WarrantyClaimController.php
**Method `checkSerial()`:**
```php
// Validasi status produk
if ($product->status === 'warranty_expired') {
    return response()->json(['status' => 'expired', 'message' => 'Product Warranty Expired']);
}

if ($product->status === 'product_claim') {
    return response()->json(['status' => 'claimed', 'message' => 'Product Already Claimed']);
}
```

### 5. Routes - web.php
```php
Route::get('warranty-replacement/{claim}', [WarrantyReplacementPublicController::class, 'show'])
    ->name('warranty.replacement.show');
```

## Product Status Flow

```
Normal Flow:
manufactured → in_distributor → at_retail → sold

Warranty Replacement Flow:
Old Product: sold → warranty_expired
Replacement Product: (any status) → product_claim
```

## Testing Checklist

- [ ] Retail bisa login dengan PIN
- [ ] List approved claims muncul sesuai retail
- [ ] Klik "Scan Replacement" membuka halaman scan
- [ ] QR scanner berfungsi dengan kamera
- [ ] Manual input berfungsi
- [ ] Replacement berhasil dan update status produk
- [ ] Produk expired tidak bisa diaktivasi warranty
- [ ] Produk claimed tidak bisa diaktivasi warranty
- [ ] Produk expired tidak bisa buat claim baru
- [ ] Produk claimed tidak bisa buat claim baru

## Notes

- Produk pengganti tidak perlu dari retail yang sama
- Produk pengganti bisa dari status: manufactured, in_distributor, atau at_retail
- Setelah replacement, kedua produk (lama & pengganti) tidak bisa digunakan lagi
- Warranty expires_at di-set ke now() untuk menandai produk sudah tidak berlaku

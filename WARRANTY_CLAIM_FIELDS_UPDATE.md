# Warranty Claim Form - Field Updates

## Tanggal: 24 Februari 2026

### Perubahan yang Dilakukan

Menambahkan field-field baru pada form warranty claim (`/warranty-claims/create`):

#### Field Baru:

1. **Tipe Motor** (motor_type)
   - Type: Text input
   - Required: Ya
   - Contoh: Honda Beat, Yamaha Mio, dll

2. **Apakah Pernah Modifikasi?** (has_modification)
   - Type: Radio button (Ya/Tidak)
   - Required: Ya
   - Default: Tidak

3. **Jenis Modifikasi** (modification_types)
   - Type: Checkbox (multiple selection)
   - Required: Tidak (hanya muncul jika "Ya" pada pertanyaan modifikasi)
   - Pilihan:
     - Boreup
     - Ganti Kiprok
     - Ganti Spull
     - Ganti Coil

4. **Nomor WhatsApp** (whatsapp_number)
   - Type: Text input
   - Required: Ya
   - Format: 08123456789

5. **Pembelian** (purchase_type)
   - Type: Select dropdown
   - Required: Ya
   - Pilihan: Online / Offline

6. **Tanggal Pembelian** (purchase_date)
   - Type: Date input
   - Required: Ya
   - Validasi: Tidak boleh lebih dari hari ini

7. **Tanggal Baterai Bermasalah** (battery_issue_date)
   - Type: Date input
   - Required: Ya
   - Validasi: Harus setelah atau sama dengan tanggal pembelian, tidak boleh lebih dari hari ini

### File yang Dimodifikasi:

1. **Migration**: `database/migrations/2026_02_24_054539_add_motor_info_to_warranty_claims_table.php`
   - Menambahkan kolom baru ke tabel `warranty_claims`

2. **Model**: `app/Models/WarrantyClaim.php`
   - Menambahkan field baru ke `$fillable`
   - Menambahkan casting untuk field baru

3. **Controller**: `app/Http/Controllers/Admin/WarrantyClaimController.php`
   - Update method `store()` untuk validasi dan menyimpan field baru

4. **View**: `resources/views/admin/warranty-claims/create.blade.php`
   - Menambahkan form input untuk semua field baru
   - Menambahkan JavaScript untuk toggle visibility field modifikasi

### Fitur Tambahan:

- **Conditional Display**: Field "Jenis Modifikasi" hanya muncul jika user memilih "Ya" pada pertanyaan modifikasi
- **Dynamic Form**: Menggunakan JavaScript untuk show/hide field modifikasi secara dinamis
- **Validation**: Semua field baru memiliki validasi yang sesuai di backend

### Cara Penggunaan:

1. Akses halaman: `https://stg-dcpower.dharmap.com/warranty-claims/create`
2. Isi semua field yang required
3. Jika memilih "Ya" pada modifikasi, pilih jenis modifikasi yang sesuai
4. Submit form

### Database Schema:

```sql
ALTER TABLE warranty_claims ADD COLUMN motor_type VARCHAR(255) NULL;
ALTER TABLE warranty_claims ADD COLUMN has_modification BOOLEAN DEFAULT FALSE;
ALTER TABLE warranty_claims ADD COLUMN modification_types JSON NULL;
ALTER TABLE warranty_claims ADD COLUMN whatsapp_number VARCHAR(255) NULL;
ALTER TABLE warranty_claims ADD COLUMN purchase_type ENUM('online', 'offline') NULL;
ALTER TABLE warranty_claims ADD COLUMN purchase_date DATE NULL;
ALTER TABLE warranty_claims ADD COLUMN battery_issue_date DATE NULL;
```

### Testing:

Untuk testing, pastikan:
1. Form dapat diakses dengan benar
2. Field modifikasi muncul/hilang sesuai pilihan
3. Validasi berjalan dengan baik
4. Data tersimpan dengan benar ke database
5. Field modification_types tersimpan sebagai JSON array

### Notes:

- Field `modification_types` disimpan sebagai JSON array di database
- Jika user memilih "Tidak" pada modifikasi, field `modification_types` akan di-set NULL
- Semua field baru bersifat nullable di database untuk backward compatibility

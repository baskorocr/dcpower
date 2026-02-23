# Standard Packing Feature Implementation

## Overview
Sistem standar packing telah ditambahkan untuk memudahkan pengelolaan produk yang dikemas dalam jumlah standar tertentu.

## Database Changes

### New Tables
1. **standard_packings**
   - `id`: Primary key
   - `project_id`: Foreign key ke projects
   - `packing_code`: Unique code untuk packing (auto-generated: PACK-XXXXXXXXXX)
   - `quantity`: Jumlah produk dalam packing
   - `created_by`: User yang membuat packing
   - `packed_at`: Timestamp kapan packing dibuat
   - `timestamps`

### Modified Tables
1. **projects**
   - Added: `standard_packing_quantity` (nullable integer) - Jumlah produk per standar packing

2. **products**
   - Added: `standard_packing_id` (nullable foreign key) - Relasi ke standard_packings

## Features

### 1. Project Configuration
- Admin dapat mengatur `standard_packing_quantity` saat membuat/edit project
- Jika diisi (misal: 10), maka setiap 10 produk akan membentuk 1 standard packing
- Jika kosong (null), project tidak menggunakan standar packing

### 2. Product Creation with Standard Packing
**URL**: `/admin/products/create`

**Flow**:
1. User scan serial number menggunakan Zebra scanner
2. Serial number ditambahkan ke list
3. Progress bar menunjukkan berapa banyak yang sudah di-scan
4. Ketika jumlah mencapai `standard_packing_quantity`:
   - Otomatis submit semua products
   - Sistem membuat record `StandardPacking` baru
   - Semua products di-link ke standard packing tersebut
   - Modal muncul menampilkan packing code
   - **Auto print label setelah 1 detik**

**Jika project tidak menggunakan standard packing**:
- User bisa scan berapa saja
- Manual submit dengan tombol "Submit Products"
- Tidak ada standard packing yang dibuat

### 3. Standard Packing Management
**URL**: `/admin/standard-packings`

**Features**:
- List semua standard packings
- Filter by project
- Search by packing code
- View details (list semua products dalam packing)
- Print label

### 4. Print Label
**URL**: `/admin/standard-packings/{id}/print`

**Features**:
- Auto-open print dialog
- Label format: 100mm x 50mm
- Contains:
  - Project name
  - Packing code (large, bold)
  - Quantity
  - Packed date/time
- Auto-close setelah print

## API Endpoints

### Store Products
```
POST /admin/products
Content-Type: application/json

{
    "serial_numbers": ["SN-001", "SN-002", ...]
}

Response:
{
    "success": true,
    "message": "10 products created successfully!",
    "products": [...],
    "standard_packing": {
        "id": 1,
        "code": "PACK-ABC123XYZ",
        "quantity": 10
    }
}
```

## Models

### StandardPacking
```php
// Relationships
- belongsTo(Project)
- hasMany(Product)
- belongsTo(User, 'created_by')

// Auto-generated
- packing_code: PACK-{10 random chars}
```

### Project
```php
// New field
- standard_packing_quantity: nullable integer

// New relationship
- hasMany(StandardPacking)
```

### Product
```php
// New field
- standard_packing_id: nullable foreign key

// New relationship
- belongsTo(StandardPacking)
```

## Routes
```php
// Standard Packings
GET  /admin/standard-packings                    - Index
GET  /admin/standard-packings/{id}               - Show details
GET  /admin/standard-packings/{id}/print         - Print label
```

## Views
1. `admin/products/create.blade.php` - Updated dengan support standard packing
2. `admin/standard-packings/index.blade.php` - List packings
3. `admin/standard-packings/show.blade.php` - Detail packing
4. `admin/standard-packings/print.blade.php` - Print label
5. `admin/projects/create.blade.php` - Added standard_packing_quantity field

## Usage Example

### Scenario 1: Project dengan Standard Packing (10 units)
1. Admin set project `standard_packing_quantity` = 10
2. User buka `/admin/products/create`
3. Scan 10 serial numbers
4. Setelah scan ke-10, otomatis:
   - 10 products dibuat
   - 1 standard packing dibuat (PACK-ABC123XYZ)
   - Semua 10 products linked ke packing tersebut
   - Modal muncul dengan packing code
   - Print dialog otomatis muncul
5. User bisa print ulang dari `/admin/standard-packings`

### Scenario 2: Project tanpa Standard Packing
1. Admin tidak set `standard_packing_quantity` (null)
2. User buka `/admin/products/create`
3. Scan berapa saja serial numbers
4. Klik "Submit Products" manual
5. Products dibuat tanpa standard packing
6. `standard_packing_id` = null

## Benefits
1. **Mudah tracking**: Setiap packing punya code unik
2. **Reporting**: Bisa report berdasarkan packing
3. **Audit trail**: Tahu siapa yang buat packing dan kapan
4. **Flexible**: Project bisa pilih pakai atau tidak pakai standard packing
5. **Auto print**: Label langsung print tanpa konfirmasi
6. **Batch management**: Manage products dalam batch/packing

## Migration Commands
```bash
php artisan migrate
```

Migrations yang dijalankan:
1. `2026_02_18_024900_create_standard_packings_table.php`
2. `2026_02_18_024901_add_standard_packing_quantity_to_projects.php`
3. `2026_02_18_024902_add_standard_packing_id_to_products.php`

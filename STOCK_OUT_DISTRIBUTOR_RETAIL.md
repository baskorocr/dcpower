# Stock Out Distributor to Retail Implementation

## Overview
Implementasi fitur stock out dengan tampilan berbeda untuk role distributor dan admin/project. Distributor dapat melakukan stock out dari inventory mereka ke retail, sementara admin/project melakukan stock out ke distributor.

## Changes Made

### 1. Controller Updates (`app/Http/Controllers/StockOutController.php`)

#### a. Index Method
- Menambahkan flag `$isDistributor` untuk membedakan role
- Pass flag ke view untuk conditional rendering

#### b. Scan Method
- **Untuk Distributor**: 
  - Hanya bisa scan produk yang ada di inventory mereka (`status = 'in_distributor'`)
  - Validasi stock movement untuk memastikan produk ada di distributor tersebut
  - Filter produk dalam packing berdasarkan availability di distributor
  
- **Untuk Admin/Project**:
  - Scan produk dengan status `manufactured`
  - Tidak bisa scan produk yang sudah `in_distributor`, `sold`, atau `claimed`

#### c. Process Method
- **Untuk Distributor (Stock Out to Retail)**:
  - Membuat stock movement dengan `type = 'out'`
  - Update product:
    - Increment `retail_stock`
    - Decrement `at_distributor`
    - Update status ke `sold` jika `at_distributor` = 0
  - Trace log dengan `event_type = 'stock_out_retail'`
  
- **Untuk Admin/Project (Stock Out to Distributor)**:
  - Membuat stock movement dengan `type = 'in'`
  - Update product:
    - Set status ke `in_distributor`
    - Set `at_distributor` dengan quantity
  - Trace log dengan `event_type = 'stock_out'`

### 2. View Updates (`resources/views/stock-out/index.blade.php`)

#### Header
- Conditional title:
  - Distributor: "Stock Out to Retail - Multiple Scan"
  - Admin/Project: "Stock Out to Distributor - Multiple Scan"

#### Info Banner (Distributor Only)
- Menampilkan informasi mode distributor
- Menjelaskan bahwa hanya produk di lokasi mereka yang bisa di-scan

#### Form Labels
- Conditional label untuk distributor selection:
  - Distributor: "Your Distributor Location"
  - Admin/Project: "Destination Distributor"

#### Button Text
- Conditional button text:
  - Distributor: "Process Stock Out to Retail"
  - Admin/Project: "Process Stock Out to Distributor"

### 3. Dashboard Updates (`routes/web.php`)

#### Stats Calculation
- Fix field name dari `at_retail` menjadi `retail_stock`
- Untuk distributor:
  - `stock_retail`: Sum dari `retail_stock` untuk produk di distributor tersebut
- Untuk admin/project:
  - `stock_retail`: Sum dari `retail_stock` untuk semua produk di project

## Database Schema

### Products Table
- `at_distributor`: INT - Jumlah produk di distributor
- `retail_stock`: INT - Jumlah produk yang sudah di stock out ke retail
- `status`: ENUM - Status produk (manufactured, in_distributor, sold, claimed)

### Stock Movements Table
- `type`: ENUM('in', 'out')
  - `in`: Stock masuk ke distributor (dari admin/project)
  - `out`: Stock keluar dari distributor (ke retail)
- `distributor_id`: Foreign key ke distributors table
- `product_id`: Foreign key ke products table
- `quantity`: INT - Jumlah produk

### Product Trace Logs Table
- `event_type`: VARCHAR
  - `stock_out`: Stock out dari admin/project ke distributor
  - `stock_out_retail`: Stock out dari distributor ke retail

## Flow Diagram

```
Admin/Project → Distributor → Retail
     (1)            (2)

(1) Stock Out to Distributor:
    - Admin/Project scan produk (status: manufactured)
    - Create stock_movement (type: in)
    - Update product: status = in_distributor, at_distributor++
    - Trace log: event_type = stock_out

(2) Stock Out to Retail:
    - Distributor scan produk (status: in_distributor)
    - Create stock_movement (type: out)
    - Update product: retail_stock++, at_distributor--
    - If at_distributor = 0: status = sold
    - Trace log: event_type = stock_out_retail
```

## User Roles & Permissions

### Admin
- Dapat melihat semua distributor
- Dapat stock out ke semua distributor
- Melihat stats untuk semua project

### Project User
- Dapat melihat distributor di project mereka
- Dapat stock out ke distributor di project mereka
- Melihat stats untuk project mereka

### Distributor
- Hanya melihat distributor mereka sendiri
- Hanya dapat stock out produk yang ada di inventory mereka
- Stock out destination: Retail (bukan distributor lain)
- Melihat stats untuk produk di inventory mereka

## Testing Scenarios

### 1. Admin Stock Out to Distributor
```
1. Login sebagai admin
2. Buka /stock-out
3. Scan produk dengan status "manufactured"
4. Pilih distributor tujuan
5. Process stock out
6. Verify:
   - Product status = in_distributor
   - at_distributor = quantity
   - Stock movement created (type: in)
   - Trace log created (event_type: stock_out)
```

### 2. Distributor Stock Out to Retail
```
1. Login sebagai distributor
2. Buka /stock-out
3. Scan produk yang ada di inventory (status: in_distributor)
4. Pilih lokasi distributor sendiri
5. Process stock out
6. Verify:
   - retail_stock increased
   - at_distributor decreased
   - If at_distributor = 0: status = sold
   - Stock movement created (type: out)
   - Trace log created (event_type: stock_out_retail)
```

### 3. Dashboard Stats
```
1. Login sebagai distributor
2. Buka /dashboard
3. Verify stats:
   - Stock at Distributor: Sum of at_distributor
   - Stock at Retail: Sum of retail_stock
   - Only shows products in their inventory
```

## API Endpoints

### POST /stock-out/scan
**Request:**
```json
{
  "qr_code": "SERIAL_NUMBER or PACKING_CODE"
}
```

**Response (Success - Single Product):**
```json
{
  "success": true,
  "is_packing": false,
  "product": {
    "id": 1,
    "serial_number": "SN001",
    "qr_code": "SN001",
    "name": "Product SN001",
    "sku": "SN001"
  }
}
```

**Response (Success - Packing):**
```json
{
  "success": true,
  "is_packing": true,
  "packing_code": "PACK001",
  "products": [
    {
      "id": 1,
      "serial_number": "SN001",
      "qr_code": "SN001",
      "name": "Product SN001",
      "sku": "SN001"
    }
  ]
}
```

**Response (Error):**
```json
{
  "success": false,
  "message": "Product not found / Product not available at your location"
}
```

### POST /stock-out/process
**Request:**
```json
{
  "distributor_id": 1,
  "products": "[{\"id\":1,\"serial_number\":\"SN001\"}]"
}
```

**Response:**
- Redirect back with success/error message

## Notes

1. **Validation**: Distributor hanya bisa scan produk yang ada di inventory mereka
2. **Stock Movement**: Type 'in' untuk masuk ke distributor, 'out' untuk keluar ke retail
3. **Product Status**: 
   - `manufactured` → `in_distributor` (admin stock out)
   - `in_distributor` → `sold` (distributor stock out, jika at_distributor = 0)
4. **Trace Logs**: Berbeda event_type untuk tracking yang lebih baik
5. **Dashboard**: Menampilkan retail_stock untuk monitoring stock yang sudah di retail

## Future Enhancements

1. Report stock out by distributor
2. Stock return from retail to distributor
3. Stock transfer between distributors
4. Real-time stock alerts
5. Batch stock out with CSV upload

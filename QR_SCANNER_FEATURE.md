# QR Code Scanner untuk Product Creation

## Fitur
Sistem ini memungkinkan QA untuk membuat product dengan cara scan QR code project menggunakan **Zebra Laser Scanner**, sehingga:
- Hanya perlu scan QR code project
- Product otomatis dibuat dengan nama dari project
- Validasi otomatis bahwa user ter-assign ke project tersebut
- Proses sangat cepat dan efisien

## Cara Penggunaan

### 1. Generate QR Code Project
- Buka halaman detail project: `/projects/{id}`
- QR code akan ditampilkan di bagian atas
- Klik tombol "Print QR Code" untuk mencetak

### 2. Create Product dengan Zebra Scanner
- Buka halaman create product: `/products/create`
- Klik pada input field (atau sudah auto-focus)
- Arahkan Zebra scanner ke QR code project
- Scanner akan otomatis membaca dan memverifikasi
- Setelah berhasil, klik "Create Product"
- Product akan dibuat dengan QR code unik

## Field yang Auto-Generated
- **Product Name**: Diambil dari nama project + " Product"
- **Description**: "Auto-generated from QR scan"
- **QR Code**: Unique identifier untuk product
- **Serial Number**: Auto-generated
- **Status**: "manufactured"
- **Category**: Nullable (tidak wajib)

## Teknologi
- **Scanner**: Zebra Laser Scanner (input langsung ke text field)
- **Backend**: Laravel API endpoint untuk validasi
- **Security**: Validasi user assignment ke project

## API Endpoint
```
GET /api/projects/verify-qr/{qrCode}
```

Response sukses:
```json
{
  "success": true,
  "project": {
    "id": 1,
    "name": "Project Name",
    "code": "PRJ-ABC123"
  }
}
```

Response gagal:
```json
{
  "success": false,
  "message": "You are not assigned to this project"
}
```

## Database Schema
Tabel `projects`:
- `code`: Kode unik project (PRJ-XXXXXXXX)
- `qr_code`: String unik untuk QR code (QR-PRJ-uuid)

Tabel `products`:
- `category_id`: Nullable (tidak wajib diisi)
- `name`: Auto-generated dari project name
- `qr_code`: Unique identifier

## Keamanan
- Hanya user yang ter-assign ke project yang bisa menggunakan QR code tersebut
- Admin bisa menggunakan semua QR code
- QR code bersifat unik dan tidak bisa diduplikasi

## Workflow
1. QA scan QR code project dengan Zebra scanner
2. Sistem verifikasi QR code dan user assignment
3. Jika valid, tampilkan info project
4. QA klik "Create Product"
5. Product dibuat otomatis dengan semua field terisi
6. Product mendapat QR code unik untuk tracking


# 🏭 DC Power - Warranty Claim System

## 📋 Overview
Sistem manajemen klaim garansi multi-tenant dengan QR code tracking untuk product lifecycle management.

## 🎯 Features
- ✅ Multi-project management
- ✅ Role-based access control (Spatie Permission)
- ✅ QR Code product tracking
- ✅ Distributor management
- ✅ Sales & stock management
- ✅ Warranty claim system
- ✅ Product trace logging

## 👥 User Roles

### 1. **Admin** (Super Admin)
- Akses penuh ke semua fitur
- Dapat membuat dan manage projects
- Manage semua users

### 2. **Project Manager**
- Manage 1 project tertentu
- Manage users dalam projectnya
- Manage distributors, products, sales, claims

### 3. **QA (Quality Assurance)**
- Input products via QR code scanning
- Track product manufacturing
- Hanya akses project yang ditugaskan

### 4. **Distributor**
- Lihat dan manage sales
- Lihat stock movements
- Manage stock di tokonya
- Hanya akses data distributornya sendiri

### 5. **Buyer** (End User)
- Lihat transaksi pembelian
- Submit warranty claims
- Track claim status

## 📊 Database Structure

### Core Tables:
1. **users** - User accounts
2. **roles** - User roles (Spatie)
3. **permissions** - Permissions (Spatie)
4. **projects** - Projects/tenants
5. **project_users** - User-project assignments
6. **distributors** - Distributor info
7. **product_categories** - Product categories
8. **products** - Products with QR codes
9. **product_trace_logs** - QR scan history
10. **stock_movements** - Stock in/out
11. **sales** - Sales transactions
12. **warranty_claims** - Warranty claims
13. **claim_histories** - Claim status history

## 🔐 Default Credentials
```
Email: admin@dcpower.com
Password: password
```

## 🚀 Installation

```bash
# Already installed:
composer require spatie/laravel-permission simplesoftwareio/simple-qrcode

# Run migrations
php artisan migrate:fresh --seed

# Generate QR codes (when needed)
use SimpleSoftwareIO\QrCode\Facades\QrCode;
QrCode::size(300)->generate($product->qr_code);
```

## 📝 Key Models & Relationships

### Product Model
```php
// Auto-generates QR code and serial number
$product = Product::create([
    'project_id' => 1,
    'category_id' => 1,
    'created_by' => auth()->id(),
    'name' => 'Product Name',
    'manufactured_at' => now(),
]);
// QR Code: QR-XXXXXXXXXXXX
// Serial: SN-XXXXXXXXXX
```

### Warranty Claim
```php
$claim = WarrantyClaim::create([
    'product_id' => $product->id,
    'sale_id' => $sale->id,
    'claimed_by_user_id' => auth()->id(),
    'complaint_type' => 'defect',
    'complaint_description' => 'Product not working',
    'submitted_at' => now(),
]);
// Claim Number: CLM-20260213-XXXXXX
```

## 🔄 Product Lifecycle Flow

1. **Manufacturing** (QA)
   - QA scans QR to create product
   - Status: `manufactured`
   - Logged in `product_trace_logs`

2. **Shipping to Distributor**
   - Stock movement created
   - Status: `in_stock`
   - Logged with location

3. **Sale to Customer**
   - Sale record created
   - Warranty period calculated
   - Status: `sold`

4. **Warranty Claim**
   - Customer submits claim
   - Status: `claimed`
   - Claim history tracked

## 🎨 Permissions

```php
'manage-projects'      // Create/edit projects
'manage-users'         // Manage users
'manage-distributors'  // Manage distributors
'manage-products'      // Manage products
'scan-qr'             // Scan QR codes
'manage-stock'        // Manage stock
'manage-sales'        // Create sales
'view-sales'          // View sales
'manage-claims'       // Handle claims
'view-claims'         // View own claims
'submit-claims'       // Submit claims
```

## 🔍 QR Code Tracking

Every product has unique QR code that tracks:
- Manufacturing date & location
- Shipping to distributor
- Sale to end customer
- Warranty claims
- Complete audit trail

## 📱 Next Steps

1. **Create Controllers**
   ```bash
   php artisan make:controller ProjectController
   php artisan make:controller ProductController
   php artisan make:controller QRScanController
   php artisan make:controller WarrantyClaimController
   ```

2. **Create Routes** in `routes/web.php`

3. **Create Views** for each module

4. **Implement Middleware** for project-based access control

5. **Add QR Code Generation** in product creation

6. **Build Dashboard** with statistics

## 🛡️ Security Notes

- All project data isolated by `project_id`
- Users only see their assigned projects
- Distributors only see their own data
- Buyers only see their purchases
- QR codes are unique and traceable

## 📞 Support

For questions or issues, contact the development team.

---
**Built with Laravel 11 + Spatie Permission + QR Code**

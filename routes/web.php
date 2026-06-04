<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
   return view('landing');
   });

// Warranty Menu (Public)
Route::get('warranty', function () {
    return view('warranty-menu');
})->name('warranty.menu');

// Warranty Activation (Public - Retail with PIN)
Route::get('warranty/activation/login', [\App\Http\Controllers\WarrantyActivationController::class, 'login'])->name('warranty.activation.login');
Route::post('warranty/activation/verify', [\App\Http\Controllers\WarrantyActivationController::class, 'verify'])->name('warranty.activation.verify');
Route::get('warranty/activation', [\App\Http\Controllers\WarrantyActivationController::class, 'index'])->name('warranty.activation');
Route::post('warranty/activate', [\App\Http\Controllers\WarrantyActivationController::class, 'activate'])->name('warranty.activate');
Route::get('warranty/activation/logout', [\App\Http\Controllers\WarrantyActivationController::class, 'logout'])->name('warranty.activation.logout');

// Warranty Replacement (Public - Retail with PIN)
Route::get('warranty-replacement/login', [\App\Http\Controllers\WarrantyReplacementPublicController::class, 'login'])->name('warranty.replacement.login');
Route::post('warranty-replacement/verify', [\App\Http\Controllers\WarrantyReplacementPublicController::class, 'verify'])->name('warranty.replacement.verify');
Route::get('warranty-replacement/logout', [\App\Http\Controllers\WarrantyReplacementPublicController::class, 'logout'])->name('warranty.replacement.logout');
Route::get('warranty-replacement', [\App\Http\Controllers\WarrantyReplacementPublicController::class, 'index'])->name('warranty.replacement.index');
Route::get('warranty-replacement/{claim}', [\App\Http\Controllers\WarrantyReplacementPublicController::class, 'show'])->name('warranty.replacement.show');
Route::post('warranty-replacement/{claim}/scan', [\App\Http\Controllers\WarrantyReplacementPublicController::class, 'scan'])->name('warranty.replacement.scan');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard/distributor-stocks', [\App\Http\Controllers\DashboardController::class, 'distributorStocks'])->name('dashboard.distributor-stocks');
    Route::get('/dashboard/retail-stocks', [\App\Http\Controllers\DashboardController::class, 'retailStocks'])->name('dashboard.retail-stocks');
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Reports
    Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/distributor/{distributor}', [\App\Http\Controllers\ReportController::class, 'distributor'])->name('reports.distributor');
    Route::get('/reports/retail/{retail}', [\App\Http\Controllers\ReportController::class, 'retail'])->name('reports.retail');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Users
    Route::middleware('can:manage-users')->group(function () {
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['show']);
    });

    // Roles (Admin only)
    Route::middleware('permission:manage-roles')->group(function () {
        Route::get('roles', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('roles.index');
        Route::post('roles', [\App\Http\Controllers\Admin\RoleController::class, 'store'])->name('roles.store');
        Route::put('roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])->name('roles.update');
        Route::delete('roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('roles.destroy');
        Route::post('permissions', [\App\Http\Controllers\Admin\RoleController::class, 'storePermission'])->name('permissions.store');
        Route::delete('permissions/{permission}', [\App\Http\Controllers\Admin\RoleController::class, 'destroyPermission'])->name('permissions.destroy');
    });

    // Product Audit Logs
    Route::middleware('permission:view-product-audit-logs')->group(function () {
        Route::get('product-audit-logs', [\App\Http\Controllers\Admin\ProductAuditLogController::class, 'index'])->name('product-audit-logs.index');
    });

    // Projects
    Route::middleware('permission:manage-projects')->group(function () {
        Route::resource('projects', \App\Http\Controllers\Admin\ProjectController::class);
    });

    // Distributors
    Route::middleware('permission:manage-distributors')->group(function () {
        Route::resource('distributors', \App\Http\Controllers\DistributorController::class);
        Route::post('distributors/bulk-delete', [\App\Http\Controllers\DistributorController::class, 'bulkDelete'])->name('distributors.bulk-delete');
    });

    // Retails
    Route::middleware('permission:manage-retails')->group(function () {
        Route::resource('retails', \App\Http\Controllers\RetailController::class);
        Route::post('retails/bulk-delete', [\App\Http\Controllers\RetailController::class, 'bulkDelete'])->name('retails.bulk-delete');
    });

    // Products
    Route::middleware('permission:manage-products')->group(function () {
        Route::get('products/switch', [\App\Http\Controllers\Admin\ProductController::class, 'switchForm'])->name('products.switch');
        Route::post('products/switch', [\App\Http\Controllers\Admin\ProductController::class, 'switchSerial'])->name('products.switch.submit');
        Route::get('products/print', [\App\Http\Controllers\Admin\ProductController::class, 'print'])->name('products.print');
        Route::post('products/bulk-delete', [\App\Http\Controllers\Admin\ProductController::class, 'bulkDelete'])->name('products.bulk-delete');
        Route::post('products/{product}/repair-status', [\App\Http\Controllers\Admin\ProductController::class, 'updateRepairStatus'])->name('products.update-repair-status');
        Route::post('products/{product}/quality-check', [\App\Http\Controllers\Admin\ProductController::class, 'qualityCheck'])->name('products.quality-check');
        Route::post('products/check-packing-quality', [\App\Http\Controllers\Admin\ProductController::class, 'checkPackingQuality'])->name('products.check-packing-quality');
        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
    });

    // Standard Packings
    Route::middleware('permission:manage-products')->group(function () {
        Route::get('standard-packings', [\App\Http\Controllers\Admin\StandardPackingController::class, 'index'])->name('standard-packings.index');
        Route::post('standard-packings/print-multiple', [\App\Http\Controllers\Admin\StandardPackingController::class, 'printMultiple'])->name('standard-packings.print-multiple');
        Route::get('standard-packings/{standardPacking}', [\App\Http\Controllers\Admin\StandardPackingController::class, 'show'])->name('standard-packings.show');
        Route::get('standard-packings/{standardPacking}/print', [\App\Http\Controllers\Admin\StandardPackingController::class, 'print'])->name('standard-packings.print');
    });

    // API for QR verification
    Route::get('/api/projects/verify-qr/{qrCode}', [\App\Http\Controllers\Admin\ProductController::class, 'verifyProjectQR']);
    Route::get('/api/check-serial/{serial}', [\App\Http\Controllers\Admin\ProductController::class, 'checkSerial']);

    // QR Scan
    Route::middleware('permission:scan-qr')->group(function () {
        Route::get('qr-scan', [\App\Http\Controllers\QRScanController::class, 'index'])->name('qr-scan.index');
        Route::post('qr-scan', [\App\Http\Controllers\QRScanController::class, 'scan'])->name('qr-scan.scan');
    });

    // Stock Out
    Route::middleware('permission:stock-out')->group(function () {
        Route::get('stock-out', [\App\Http\Controllers\StockOutController::class, 'index'])->name('stock-out.index');
        Route::post('stock-out/scan', [\App\Http\Controllers\StockOutController::class, 'scan'])->name('stock-out.scan');
        Route::post('stock-out/process', [\App\Http\Controllers\StockOutController::class, 'process'])->name('stock-out.process');
    });

    // Warranty Claims
    Route::middleware('permission:view-claims|manage-claims')->group(function () {
        Route::post('warranty-claims/check-serial', [\App\Http\Controllers\Admin\WarrantyClaimController::class, 'checkSerial'])->name('warranty-claims.check-serial');
        Route::delete('warranty-claims/{warrantyClaim}/cancel', [\App\Http\Controllers\Admin\WarrantyClaimController::class, 'cancel'])->name('warranty-claims.cancel');
        Route::resource('warranty-claims', \App\Http\Controllers\Admin\WarrantyClaimController::class);
    });

    // Claim Approvals (Admin only)
    Route::middleware('permission:approve-claims')->group(function () {
        Route::get('claim-approvals', [\App\Http\Controllers\Admin\ClaimApprovalController::class, 'index'])->name('claim-approvals.index');
        Route::get('claim-approvals/{claim}', [\App\Http\Controllers\Admin\ClaimApprovalController::class, 'show'])->name('claim-approvals.show');
        Route::post('claim-approvals/{claim}/approve', [\App\Http\Controllers\Admin\ClaimApprovalController::class, 'approve'])->name('claim-approvals.approve');
        Route::post('claim-approvals/{claim}/reject', [\App\Http\Controllers\Admin\ClaimApprovalController::class, 'reject'])->name('claim-approvals.reject');
    });

    // Claim History (Admin only)
    Route::middleware('permission:view-claim-history')->group(function () {
        Route::get('claim-history', [\App\Http\Controllers\Admin\ClaimHistoryController::class, 'index'])->name('claim-history.index');
        Route::get('claim-history/export', [\App\Http\Controllers\Admin\ClaimHistoryController::class, 'export'])->name('claim-history.export');
    });

    // Contact Messages (Admin only)
    Route::middleware('permission:manage-contact-messages')->group(function () {
        Route::get('contact-messages', [\App\Http\Controllers\Admin\ContactMessageAdminController::class, 'index'])->name('contact-messages.index');
        Route::get('contact-messages/{message}', [\App\Http\Controllers\Admin\ContactMessageAdminController::class, 'show'])->name('contact-messages.show');
        Route::post('contact-messages/{message}/status', [\App\Http\Controllers\Admin\ContactMessageAdminController::class, 'updateStatus'])->name('contact-messages.update-status');
        Route::delete('contact-messages/{message}', [\App\Http\Controllers\Admin\ContactMessageAdminController::class, 'destroy'])->name('contact-messages.destroy');
    });
});

// Public API for retail locations
Route::get('/api/retails/locations', function () {
    return \App\Models\Retail::where('status', 'active')
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->select('id', 'name', 'city', 'province', 'latitude', 'longitude')
        ->get();
});

// Contact Message
Route::post('/contact', [\App\Http\Controllers\ContactMessageController::class, 'store'])->name('contact.store');

require __DIR__ . '/auth.php';

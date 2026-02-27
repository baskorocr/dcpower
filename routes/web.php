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

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Users
    Route::middleware('can:manage-users')->group(function () {
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['show', 'destroy']);
    });

    // Roles (Admin only)
    Route::middleware('permission:manage-roles')->group(function () {
        Route::get('roles', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('roles.index');
        Route::post('roles', [\App\Http\Controllers\Admin\RoleController::class, 'store'])->name('roles.store');
        Route::put('roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])->name('roles.update');
        Route::delete('roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('roles.destroy');
        Route::post('permissions', [\App\Http\Controllers\Admin\RoleController::class, 'storePermission'])->name('permissions.store');
    });

    // Projects
    Route::middleware('permission:manage-projects')->group(function () {
        Route::resource('projects', \App\Http\Controllers\Admin\ProjectController::class);
    });

    // Distributors
    Route::middleware('permission:manage-distributors')->group(function () {
        Route::resource('distributors', \App\Http\Controllers\DistributorController::class);
    });

    // Retails
    Route::middleware('permission:manage-retails')->group(function () {
        Route::resource('retails', \App\Http\Controllers\RetailController::class);
    });

    // Products
    Route::middleware('permission:manage-products')->group(function () {
        Route::get('products/print', [\App\Http\Controllers\Admin\ProductController::class, 'print'])->name('products.print');
        Route::post('products/bulk-delete', [\App\Http\Controllers\Admin\ProductController::class, 'bulkDelete'])->name('products.bulk-delete');
        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
    });

    // Standard Packings
    Route::middleware('permission:manage-products')->group(function () {
        Route::get('standard-packings', [\App\Http\Controllers\Admin\StandardPackingController::class, 'index'])->name('standard-packings.index');
        Route::get('standard-packings/{standardPacking}', [\App\Http\Controllers\Admin\StandardPackingController::class, 'show'])->name('standard-packings.show');
        Route::get('standard-packings/{standardPacking}/print', [\App\Http\Controllers\Admin\StandardPackingController::class, 'print'])->name('standard-packings.print');
    });

    // API for QR verification
    Route::get('/api/projects/verify-qr/{qrCode}', [\App\Http\Controllers\Admin\ProductController::class, 'verifyProjectQR']);

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

require __DIR__ . '/auth.php';

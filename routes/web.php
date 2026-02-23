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

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard/distributor-stocks', function () {
        $user = auth()->user();
        
        if ($user->hasRole('admin')) {
            $projectIds = \App\Models\Project::pluck('id');
        } elseif ($user->hasRole('distributor')) {
            $distributor = \App\Models\Distributor::where('user_id', $user->id)->first();
            $projectIds = $distributor ? collect([$distributor->project_id]) : collect([]);
        } else {
            $projectIds = \DB::table('project_users')->where('user_id', $user->id)->pluck('project_id');
        }
        
        $distributors = \App\Models\Distributor::with('project')
            ->whereIn('project_id', $projectIds)
            ->get()
            ->map(function($dist) {
                $dist->stock_count = \App\Models\Product::where('status', 'in_distributor')
                    ->whereHas('stockMovements', fn($q) => $q->where('distributor_id', $dist->id))
                    ->count();
                return $dist;
            });
        
        return response()->json($distributors);
    })->name('dashboard.distributor-stocks');

    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        // Buyer Dashboard
        if ($user->hasRole('buyer')) {
            $recentPurchases = \App\Models\Sale::with(['product', 'distributor'])
                ->where('buyer_user_id', $user->id)
                ->latest()
                ->take(5)
                ->get();
            
            $recentClaims = \App\Models\WarrantyClaim::with(['product', 'sale'])
                ->where('claimed_by_user_id', $user->id)
                ->latest()
                ->take(5)
                ->get();
            
            return view('dashboard-buyer', compact('recentPurchases', 'recentClaims'));
        }
        
        // Determine project IDs based on role
        if ($user->hasRole('admin')) {
            $projectIds = \App\Models\Project::pluck('id');
        } elseif ($user->hasRole('distributor')) {
            // Distributor only sees their own distributor's project
            $distributor = \App\Models\Distributor::where('user_id', $user->id)->first();
            $projectIds = $distributor ? collect([$distributor->project_id]) : collect([]);
        } else {
            // Project users see their assigned projects
            $projectIds = \DB::table('project_users')->where('user_id', $user->id)->pluck('project_id');
        }
        
        // Products with filtering
        $query = \App\Models\Product::with(['project', 'creator'])->whereIn('project_id', $projectIds);
        
        // Distributor only sees products in their stock
        if ($user->hasRole('distributor')) {
            $distributor = \App\Models\Distributor::where('user_id', $user->id)->first();
            if ($distributor) {
                $productIds = \DB::table('stock_movements')
                    ->where('distributor_id', $distributor->id)
                    ->pluck('product_id');
                $query->whereIn('id', $productIds);
            }
        }
        
        if (request('status')) $query->where('status', request('status'));
        if (request('project')) $query->where('project_id', request('project'));
        $products = $query->latest()->take(10)->get();
        
        // Stats
        if ($user->hasRole('distributor')) {
            $distributor = \App\Models\Distributor::where('user_id', $user->id)->first();
            $myStock = $distributor ? \DB::table('stock_movements')
                ->where('distributor_id', $distributor->id)
                ->selectRaw('SUM(CASE WHEN type = "in" THEN quantity ELSE -quantity END) as total')
                ->value('total') : 0;
            
            $stats = [
                'stock_manufactured' => 0,
                'stock_distributor' => $myStock ?? 0,
                'total_sold' => $distributor ? \App\Models\Sale::where('distributor_id', $distributor->id)->count() : 0,
                'pending_claims' => $distributor ? \App\Models\WarrantyClaim::whereHas('sale', fn($q) => $q->where('distributor_id', $distributor->id))->whereIn('status', ['pending', 'under_review'])->count() : 0,
                'total_sales' => $distributor ? \App\Models\Sale::where('distributor_id', $distributor->id)->sum('sale_price') : 0,
                'recent_sales' => $distributor ? \App\Models\Sale::with(['product', 'distributor'])->where('distributor_id', $distributor->id)->latest()->take(5)->get() : collect([]),
                'low_stock_distributors' => collect([]),
            ];
        } else {
            // Apply filters to stats
            $statsProjectIds = request('project') ? collect([request('project')]) : $projectIds;
            
            $stats = [
                'stock_manufactured' => \App\Models\Product::whereIn('project_id', $statsProjectIds)->where('status', 'manufactured')->count(),
                'stock_distributor' => \App\Models\Product::whereIn('project_id', $statsProjectIds)->where('status', 'in_distributor')->count(),
                'total_sold' => \App\Models\Product::whereIn('project_id', $statsProjectIds)->where('status', 'sold')->count(),
                'pending_claims' => \App\Models\WarrantyClaim::whereHas('product', fn($q) => $q->whereIn('project_id', $statsProjectIds))->whereIn('status', ['pending', 'under_review'])->count(),
                'total_sales' => \App\Models\Sale::whereHas('product', fn($q) => $q->whereIn('project_id', $statsProjectIds))->sum('sale_price'),
                'recent_sales' => \App\Models\Sale::with(['product', 'distributor'])->whereHas('product', fn($q) => $q->whereIn('project_id', $statsProjectIds))->latest()->take(5)->get(),
                'low_stock_distributors' => \App\Models\Distributor::whereIn('project_id', $statsProjectIds)
                    ->withCount(['stockMovements as stock_count' => fn($q) => $q->selectRaw('SUM(CASE WHEN type = "in" THEN quantity ELSE -quantity END)')])
                    ->having('stock_count', '<', 10)->take(5)->get(),
            ];
        }
        
        $projects = \App\Models\Project::whereIn('id', $projectIds)->get();
        
        return view('dashboard', compact('products', 'stats', 'projects'));
    })->name('dashboard');

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

    // Products
    Route::middleware('permission:manage-products')->group(function () {
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

    // Sales
    Route::middleware('permission:view-sales|manage-sales')->group(function () {
        Route::resource('sales', \App\Http\Controllers\Admin\SaleController::class);
    });

    // Warranty Claims
    Route::middleware('permission:view-claims|manage-claims|submit-claims')->group(function () {
        Route::resource('warranty-claims', \App\Http\Controllers\Admin\WarrantyClaimController::class);
    });

    // Claim Approvals (Admin only)
    Route::middleware('permission:approve-claims')->group(function () {
        Route::get('claim-approvals', [\App\Http\Controllers\Admin\ClaimApprovalController::class, 'index'])->name('claim-approvals.index');
        Route::get('claim-approvals/{claim}', [\App\Http\Controllers\Admin\ClaimApprovalController::class, 'show'])->name('claim-approvals.show');
        Route::post('claim-approvals/{claim}/approve', [\App\Http\Controllers\Admin\ClaimApprovalController::class, 'approve'])->name('claim-approvals.approve');
        Route::post('claim-approvals/{claim}/reject', [\App\Http\Controllers\Admin\ClaimApprovalController::class, 'reject'])->name('claim-approvals.reject');
    });
});

require __DIR__ . '/auth.php';

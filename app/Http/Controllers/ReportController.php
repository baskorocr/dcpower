<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Distributor;
use App\Models\Retail;
use App\Models\Project;
use App\Exports\ReportsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->hasAnyRole(['admin', 'marketing', 'project_manager'])) {
            abort(403);
        }
        
        $projectIds = $this->getProjectIds($user);
        $projects = Project::whereIn('id', $projectIds)->get();
        
        // Get variants for filter - filter by project if selected
        $variantsQuery = \App\Models\StandardPacking::whereHas('products', function($q) use ($projectIds, $request) {
            if ($request->filled('project_id')) {
                $q->where('project_id', $request->project_id);
            }
            $q->whereHas('stockMovements', function($sq) use ($projectIds, $request) {
                $sq->whereHas('distributor', function($dq) use ($projectIds, $request) {
                    if ($request->filled('project_id')) {
                        $dq->where('project_id', $request->project_id);
                    } else {
                        $dq->whereIn('project_id', $projectIds);
                    }
                });
            });
        })->select('variant')->groupBy('variant')->orderBy('variant');
        
        $variants = $variantsQuery->get();
        
        $query = Distributor::with(['project'])
            ->whereIn('project_id', $projectIds);
        
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }
        
        $distributors = $query->get()->map(function($dist) use ($request) {
            $productQuery = DB::table('stock_movements')
                ->where('distributor_id', $dist->id);
            
            if ($request->filled('start_date')) {
                $productQuery->where('created_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $productQuery->where('created_at', '<=', $request->end_date . ' 23:59:59');
            }
            
            $productIds = $productQuery->pluck('product_id')->unique();
            
            // Filter by variant if selected
            if ($request->filled('variant')) {
                $variantProductIds = Product::whereIn('id', $productIds)
                    ->whereHas('standardPacking', function($q) use ($request) {
                        $q->where('variant', $request->variant);
                    })
                    ->pluck('id');
                $productIds = $variantProductIds;
            }
            
            $dist->stock_count = Product::whereIn('id', $productIds)
                ->where('status', 'in_distributor')
                ->count();
            
            $dist->sold_count = Product::whereIn('id', $productIds)
                ->where('status', 'sold')
                ->count();
            
            $dist->retail_count = Retail::where('distributor_id', $dist->id)->count();
            
            return $dist;
        });
        
        // Chart data
        $chartData = $this->getChartData($projectIds, $request);
        
        if ($request->has('export')) {
            return Excel::download(new ReportsExport($distributors), 'sales-report-' . date('Y-m-d') . '.xlsx');
        }
        
        return view('reports.index', compact('distributors', 'projects', 'chartData', 'variants'));
    }
    
    private function getChartData($projectIds, $request)
    {
        $startDate = $request->filled('start_date') ? $request->start_date : now()->subDays(30)->format('Y-m-d');
        $endDate = $request->filled('end_date') ? $request->end_date : now()->format('Y-m-d');
        
        $query = DB::table('products')
            ->selectRaw('DATE(products.updated_at) as date, COUNT(*) as count')
            ->where('products.status', 'sold')
            ->whereBetween('products.updated_at', [$startDate, $endDate . ' 23:59:59']);
        
        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('products.project_id', $request->project_id);
        } else {
            $query->whereIn('products.project_id', $projectIds);
        }
        
        // Filter by variant
        if ($request->filled('variant')) {
            $query->join('standard_packings', 'products.standard_packing_id', '=', 'standard_packings.id')
                ->where('standard_packings.variant', $request->variant);
        }
        
        $sales = $query->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return [
            'labels' => $sales->pluck('date')->map(fn($d) => date('d M', strtotime($d)))->toArray(),
            'data' => $sales->pluck('count')->toArray(),
        ];
    }
    
    public function distributor(Distributor $distributor)
    {
        $user = auth()->user();
        
        if (!$user->hasAnyRole(['admin', 'marketing', 'project_manager'])) {
            abort(403);
        }
        
        // Check if user has access to this distributor's project
        if (!$user->hasRole('admin')) {
            $userProjectIds = DB::table('project_users')->where('user_id', $user->id)->pluck('project_id');
            if (!$userProjectIds->contains($distributor->project_id)) {
                abort(403, 'Unauthorized access to this distributor');
            }
        }

        // Export Excel
        if (request()->has('export')) {
            return Excel::download(new \App\Exports\DistributorRetailsExport($distributor), 
                'distributor-' . $distributor->code . '-retails.xlsx');
        }
        
        $distributor->load('project');
        
        $productIds = DB::table('stock_movements')
            ->where('distributor_id', $distributor->id)
            ->pluck('product_id')
            ->unique();
        
        $stats = [
            'total_received' => $productIds->count(),
            'in_stock' => Product::whereIn('id', $productIds)->where('status', 'in_distributor')->count(),
            'at_retail' => Product::whereIn('id', $productIds)->where('status', 'at_retail')->count(),
            'sold' => Product::whereIn('id', $productIds)->where('status', 'sold')->count(),
        ];
        
        $retails = Retail::where('distributor_id', $distributor->id)
            ->get()
            ->map(function($retail) {
                $retail->stock_count = Product::where('status', 'at_retail')
                    ->whereHas('stockMovements', fn($q) => $q->where('retail_id', $retail->id))
                    ->count();
                
                $retail->sold_count = Product::where('status', 'sold')
                    ->whereHas('stockMovements', fn($q) => $q->where('retail_id', $retail->id))
                    ->count();
                
                return $retail;
            });
        
        return view('reports.distributor', compact('distributor', 'stats', 'retails'));
    }
    
    public function retail(Retail $retail)
    {
        $user = auth()->user();
        
        if (!$user->hasAnyRole(['admin', 'marketing', 'project_manager'])) {
            abort(403);
        }
        
        // Check if user has access to this retail's project
        if (!$user->hasRole('admin')) {
            $userProjectIds = DB::table('project_users')->where('user_id', $user->id)->pluck('project_id');
            $retail->load('distributor');
            if (!$userProjectIds->contains($retail->distributor->project_id)) {
                abort(403, 'Unauthorized access to this retail');
            }
        }
        
        $retail->load(['distributor.project']);
        
        $stats = [
            'in_stock' => Product::where('status', 'at_retail')
                ->whereHas('stockMovements', fn($q) => $q->where('retail_id', $retail->id))
                ->count(),
            'sold' => Product::where('status', 'sold')
                ->whereHas('stockMovements', fn($q) => $q->where('retail_id', $retail->id))
                ->count(),
        ];
        
        $products = Product::with(['project'])
            ->whereHas('stockMovements', fn($q) => $q->where('retail_id', $retail->id))
            ->whereIn('status', ['at_retail', 'sold'])
            ->latest()
            ->paginate(20);
        
        return view('reports.retail', compact('retail', 'stats', 'products'));
    }
    
    private function getProjectIds($user)
    {
        if ($user->hasRole('admin')) {
            return Project::pluck('id');
        }
        return DB::table('project_users')->where('user_id', $user->id)->pluck('project_id');
    }
}

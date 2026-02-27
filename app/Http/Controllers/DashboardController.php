<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Project;
use App\Models\Distributor;
use App\Models\Retail;
use App\Models\WarrantyClaim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->hasRole('buyer')) {
            return $this->buyerDashboard();
        }
        
        $projectIds = $this->getProjectIds($user);
        $products = $this->getProducts($user, $projectIds);
        $stats = $this->getStats($user, $projectIds);
        $projects = Project::whereIn('id', $projectIds)->get();
        
        return view('dashboard', compact('products', 'stats', 'projects'));
    }

    public function distributorStocks()
    {
        $user = auth()->user();
        
        if ($user->hasRole('admin')) {
            $projectIds = Project::pluck('id');
            $distributors = Distributor::with(['project', 'retails'])
                ->whereIn('project_id', $projectIds)
                ->get();
        } elseif ($user->hasRole('distributor')) {
            $distributor = Distributor::with(['project', 'retails'])->where('user_id', $user->id)->first();
            $distributors = $distributor ? collect([$distributor]) : collect([]);
        } else {
            $projectIds = DB::table('project_users')->where('user_id', $user->id)->pluck('project_id');
            $distributors = Distributor::with(['project', 'retails'])
                ->whereIn('project_id', $projectIds)
                ->get();
        }
        
        $distributors = $distributors->map(function($dist) {
            $dist->stock_count = Product::where('status', 'in_distributor')
                ->whereHas('stockMovements', fn($q) => $q->where('distributor_id', $dist->id))
                ->count();
            return $dist;
        });
        
        return response()->json($distributors);
    }

    public function retailStocks()
    {
        $user = auth()->user();
        
        if ($user->hasRole('admin')) {
            $retails = Retail::with('distributor')->get();
        } elseif ($user->hasRole('distributor')) {
            $distributor = Distributor::where('user_id', $user->id)->first();
            $retails = $distributor ? Retail::with('distributor')->where('distributor_id', $distributor->id)->get() : collect([]);
        } else {
            $projectIds = DB::table('project_users')->where('user_id', $user->id)->pluck('project_id');
            $retails = Retail::with('distributor')->whereHas('distributor', fn($q) => $q->whereIn('project_id', $projectIds))->get();
        }
        
        $retails = $retails->map(function($retail) {
            $retail->stock_count = Product::where('retail_stock', '>', 0)->count();
            return $retail;
        });
        
        return response()->json($retails);
    }

    private function buyerDashboard()
    {
        $recentClaims = WarrantyClaim::with('product')
            ->where('claimed_by_user_id', auth()->id())
            ->latest()
            ->take(5)
            ->get();
        
        return view('dashboard-buyer', compact('recentClaims'));
    }

    private function getProjectIds($user)
    {
        if ($user->hasRole('admin')) {
            return Project::pluck('id');
        } elseif ($user->hasRole('distributor')) {
            $distributor = Distributor::where('user_id', $user->id)->first();
            return $distributor ? collect([$distributor->project_id]) : collect([]);
        } else {
            return DB::table('project_users')->where('user_id', $user->id)->pluck('project_id');
        }
    }

    private function getProducts($user, $projectIds)
    {
        $query = Product::with(['project', 'creator'])->whereIn('project_id', $projectIds);
        
        if ($user->hasRole('distributor')) {
            $distributor = Distributor::where('user_id', $user->id)->first();
            if ($distributor) {
                $productIds = DB::table('stock_movements')
                    ->where('distributor_id', $distributor->id)
                    ->pluck('product_id');
                $query->whereIn('id', $productIds);
            }
        }
        
        if (request('status')) $query->where('status', request('status'));
        if (request('project')) $query->where('project_id', request('project'));
        
        return $query->latest()->take(10)->get();
    }

    private function getStats($user, $projectIds)
    {
        if ($user->hasRole('distributor')) {
            return $this->getDistributorStats($user);
        }
        
        $statsProjectIds = request('project') ? collect([request('project')]) : $projectIds;
        
        return [
            'stock_manufactured' => Product::whereIn('project_id', $statsProjectIds)->where('status', 'manufactured')->count(),
            'stock_distributor' => Product::whereIn('project_id', $statsProjectIds)->sum('at_distributor'),
            'stock_retail' => Product::whereIn('project_id', $statsProjectIds)->sum('retail_stock'),
            'total_sold' => Product::whereIn('project_id', $statsProjectIds)->where('status', 'sold')->count(),
            'pending_claims' => WarrantyClaim::whereHas('product', fn($q) => $q->whereIn('project_id', $statsProjectIds))->whereIn('status', ['pending', 'under_review'])->count(),
        ];
    }

    private function getDistributorStats($user)
    {
        $distributor = Distributor::where('user_id', $user->id)->first();
        
        $productIds = $distributor ? DB::table('stock_movements')
            ->where('distributor_id', $distributor->id)
            ->pluck('product_id')->unique() : collect([]);
        
        return [
            'stock_manufactured' => 0,
            'stock_distributor' => $distributor ? Product::whereIn('id', $productIds)->sum('at_distributor') : 0,
            'stock_retail' => $distributor ? Product::whereIn('id', $productIds)->sum('retail_stock') : 0,
            'total_sold' => $distributor ? Product::whereIn('id', $productIds)->where('status', 'sold')->count() : 0,
            'pending_claims' => $distributor ? WarrantyClaim::whereHas('product', fn($q) => $q->whereIn('id', $productIds))->whereIn('status', ['pending', 'under_review'])->count() : 0,
        ];
    }
}

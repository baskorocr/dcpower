<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Distributor;
use App\Models\StockMovement;
use App\Models\ProductTraceLog;
use Illuminate\Http\Request;

class StockOutController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isDistributor = $user->hasRole('distributor');
        
        if ($isDistributor) {
            $distributor = Distributor::where('user_id', $user->id)->first();
            $distributors = $distributor ? collect([$distributor]) : collect([]);
            // Get only retails under this distributor
            $retailers = $distributor 
                ? \App\Models\Retail::where('distributor_id', $distributor->id)->where('status', 'active')->get()
                : collect([]);
        } elseif ($user->hasRole('admin')) {
            $distributors = Distributor::with('project')->where('status', 'active')->get();
            $retailers = collect([]);
        } else {
            $projectIds = $user->projects->pluck('id');
            $distributors = Distributor::with('project')->whereIn('project_id', $projectIds)->where('status', 'active')->get();
            $retailers = collect([]);
        }
        
        return view('stock-out.index', compact('distributors', 'isDistributor', 'retailers'));
    }

    public function scan(Request $request)
    {
        $validated = $request->validate(['qr_code' => 'required|string']);
        $user = auth()->user();
        $isDistributor = $user->hasRole('distributor');
        
        // Check if it's a packing code
        $packing = \App\Models\StandardPacking::where('packing_code', $validated['qr_code'])->first();
        
        if ($packing) {
            // For distributor, only show products that are at their distributor
            $products = $packing->products;
            
            if ($isDistributor) {
                $distributor = Distributor::where('user_id', $user->id)->first();
                $products = $products->where('status', 'in_distributor')
                    ->filter(function($product) use ($distributor) {
                        return $product->stockMovements()
                            ->where('distributor_id', $distributor->id)
                            ->where('type', 'in')
                            ->exists();
                    });
            }
            
            $products = $products->map(function($product) {
                return [
                    'id' => $product->id,
                    'serial_number' => $product->serial_number,
                    'qr_code' => $product->serial_number,
                    'name' => 'Product ' . $product->serial_number,
                    'sku' => $product->serial_number,
                ];
            });
            
            if ($products->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No available products in this packing']);
            }
            
            return response()->json([
                'success' => true,
                'is_packing' => true,
                'packing_code' => $packing->packing_code,
                'products' => $products
            ]);
        }
        
        // Check if it's a single product
        $product = Product::where('serial_number', $validated['qr_code'])->first();
        
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product or packing not found']);
        }
        
        // For distributor, check if product is at their location
        if ($isDistributor) {
            $distributor = Distributor::where('user_id', $user->id)->first();
            
            if ($product->status !== 'in_distributor') {
                return response()->json(['success' => false, 'message' => 'Product not available at your location']);
            }
            
            $hasStock = $product->stockMovements()
                ->where('distributor_id', $distributor->id)
                ->where('type', 'in')
                ->exists();
                
            if (!$hasStock) {
                return response()->json(['success' => false, 'message' => 'Product not available at your location']);
            }
        } else {
            // For admin/project, check if product is manufactured
            if (in_array($product->status, ['in_distributor', 'sold', 'claimed'])) {
                return response()->json(['success' => false, 'message' => 'Product already distributed or sold']);
            }
        }
        
        return response()->json([
            'success' => true,
            'is_packing' => false,
            'product' => [
                'id' => $product->id,
                'serial_number' => $product->serial_number,
                'qr_code' => $product->serial_number,
                'name' => 'Product ' . $product->serial_number,
                'sku' => $product->serial_number,
            ]
        ]);
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'distributor_id' => 'required|exists:distributors,id',
            'products' => 'nullable|json',
            'retail_id' => 'nullable|exists:retails,id',
        ]);
        
        $user = auth()->user();
        $isDistributor = $user->hasRole('distributor');
        $distributor = Distributor::findOrFail($validated['distributor_id']);
        
        if ($isDistributor) {
            $userDistributor = Distributor::where('user_id', $user->id)->first();
            if (!$userDistributor || $userDistributor->id != $distributor->id) {
                return back()->with('error', 'Unauthorized access');
            }
        } elseif (!$user->hasRole('admin')) {
            $projectIds = $user->projects->pluck('id');
            if (!$projectIds->contains($distributor->project_id)) {
                return back()->with('error', 'Unauthorized access');
            }
        }
        
        $products = $validated['products'] ? json_decode($validated['products'], true) : [];
        
        if (empty($products)) {
            return back()->with('error', 'Please scan or enter serial number first');
        }
        
        $retailName = 'Retail';
        if ($isDistributor && isset($validated['retail_id'])) {
            $retail = \App\Models\Retail::find($validated['retail_id']);
            $retailName = $retail ? $retail->name : 'Retail';
        }
        
        $productIds = collect($products)->pluck('id')->unique();
        
        foreach ($productIds as $productId) {
            $count = collect($products)->where('id', $productId)->count();
            
            if ($isDistributor) {
                // Distributor stock out to retail
                StockMovement::create([
                    'product_id' => $productId,
                    'distributor_id' => $distributor->id,
                    'retail_id' => $validated['retail_id'] ?? null,
                    'type' => 'out',
                    'quantity' => $count,
                    'moved_at' => now(),
                ]);
                
                $product = Product::find($productId);
                if ($product && $product->status === 'in_distributor') {
                    $newRetailStock = ($product->retail_stock ?? 0) + $count;
                    $newDistributorStock = max(0, ($product->at_distributor ?? 0) - $count);
                    
                    $product->update([
                        'retail_stock' => $newRetailStock,
                        'at_distributor' => $newDistributorStock,
                        'status' => 'at_retail'
                    ]);
                    
                    ProductTraceLog::create([
                        'product_id' => $product->id,
                        'user_id' => $user->id,
                        'scanned_by' => $user->id,
                        'event_type' => 'stock_out_retail',
                        'action' => 'stock_out_retail',
                        'location' => $retailName,
                        'notes' => "Stock out to retail: {$retailName} from distributor: {$distributor->name}",
                        'scanned_at' => now(),
                    ]);
                }
            } else {
                // Admin/Project stock out to distributor
                StockMovement::create([
                    'product_id' => $productId,
                    'distributor_id' => $distributor->id,
                    'type' => 'in',
                    'quantity' => $count,
                    'moved_at' => now(),
                ]);
                
                $product = Product::find($productId);
                if ($product && $product->status === 'manufactured') {
                    $product->update([
                        'status' => 'in_distributor',
                        'at_distributor' => $count
                    ]);
                    
                    ProductTraceLog::create([
                        'product_id' => $product->id,
                        'user_id' => $user->id,
                        'scanned_by' => $user->id,
                        'event_type' => 'stock_out',
                        'action' => 'stock_out',
                        'location' => $distributor->name,
                        'notes' => "Shipped to distributor: {$distributor->name}",
                        'scanned_at' => now(),
                    ]);
                }
            }
        }
        
        $message = $isDistributor 
            ? "Stock out to retail '{$retailName}' processed successfully" 
            : 'Stock out to distributor processed successfully';
            
        return back()->with('success', $message);
    }
}

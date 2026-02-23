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
        
        if ($user->hasRole('distributor')) {
            $distributor = Distributor::where('user_id', $user->id)->first();
            $distributors = $distributor ? collect([$distributor]) : collect([]);
        } elseif ($user->hasRole('admin')) {
            $distributors = Distributor::with('project')->where('status', 'active')->get();
        } else {
            $projectIds = $user->projects->pluck('id');
            $distributors = Distributor::with('project')->whereIn('project_id', $projectIds)->where('status', 'active')->get();
        }
        
        return view('stock-out.index', compact('distributors'));
    }

    public function scan(Request $request)
    {
        $validated = $request->validate(['qr_code' => 'required|string']);
        
        // Check if it's a packing code
        $packing = \App\Models\StandardPacking::where('packing_code', $validated['qr_code'])->first();
        
        if ($packing) {
            // Return all products in this packing
            $products = $packing->products->map(function($product) {
                return [
                    'id' => $product->id,
                    'serial_number' => $product->serial_number,
                    'qr_code' => $product->serial_number,
                    'name' => 'Product ' . $product->serial_number,
                    'sku' => $product->serial_number,
                ];
            });
            
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
        
        if (in_array($product->status, ['in_distributor', 'sold', 'claimed'])) {
            return response()->json(['success' => false, 'message' => 'Product already distributed or sold']);
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
        ]);
        
        $user = auth()->user();
        $distributor = Distributor::findOrFail($validated['distributor_id']);
        
        if ($user->hasRole('distributor')) {
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
        
        $productIds = collect($products)->pluck('id')->unique();
        
        foreach ($productIds as $productId) {
            $count = collect($products)->where('id', $productId)->count();
            
            StockMovement::create([
                'product_id' => $productId,
                'distributor_id' => $distributor->id,
                'type' => 'in',
                'quantity' => $count,
                'moved_at' => now(),
            ]);
            
            $product = Product::find($productId);
            if ($product && $product->status === 'manufactured') {
                $product->update(['status' => 'in_distributor']);
                
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
        
        return back()->with('success', 'Stock out processed successfully');
    }
}

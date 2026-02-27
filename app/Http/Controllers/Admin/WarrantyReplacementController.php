<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WarrantyClaim;
use App\Models\Product;
use Illuminate\Http\Request;

class WarrantyReplacementController extends Controller
{
    public function index()
    {
        $query = WarrantyClaim::with(['product', 'claimedBy'])
            ->where('status', 'approved')
            ->whereNull('replaced_at');

        // Filter by distributor if not admin
        if (!auth()->user()->hasRole('admin')) {
            $distributor = \App\Models\Distributor::where('user_id', auth()->id())->first();
            
            if (!$distributor) {
                abort(403, 'Distributor not found');
            }

            $query->whereHas('product.sale', fn($q) => $q->where('distributor_id', $distributor->id));
        }

        $claims = $query->latest()->paginate(20);

        return view('admin.warranty-replacements.index', compact('claims'));
    }

    public function show(WarrantyClaim $warrantyClaim)
    {
        $warrantyClaim->load(['product', 'claimedBy', 'approver']);

        return view('admin.warranty-replacements.show', compact('warrantyClaim'));
    }

    public function scan(Request $request, WarrantyClaim $warrantyClaim)
    {
        $request->validate([
            'serial_number' => 'required|string'
        ]);

        // Get distributor (admin can process for any distributor)
        if (auth()->user()->hasRole('admin')) {
            // For admin, get distributor from the claim's sale
            $sale = $warrantyClaim->product->sale;
            if (!$sale) {
                return response()->json(['success' => false, 'message' => 'Sale not found for this claim'], 400);
            }
            $distributor = $sale->distributor;
        } else {
            $distributor = \App\Models\Distributor::where('user_id', auth()->id())->first();
        }
        
        if (!$distributor) {
            return response()->json(['success' => false, 'message' => 'Distributor not found'], 403);
        }

        // Find replacement product
        $replacementProduct = Product::where('serial_number', $request->serial_number)->first();

        if (!$replacementProduct) {
            return response()->json(['success' => false, 'message' => 'Product not found']);
        }

        // Validate product is in distributor stock
        $inStock = \DB::table('stock_movements')
            ->where('distributor_id', $distributor->id)
            ->where('product_id', $replacementProduct->id)
            ->exists();

        if (!$inStock) {
            return response()->json(['success' => false, 'message' => 'Product not in your stock']);
        }

        if ($replacementProduct->status !== 'in_distributor') {
            return response()->json(['success' => false, 'message' => 'Product status must be "in_distributor"']);
        }

        \DB::transaction(function () use ($warrantyClaim, $replacementProduct, $distributor) {
            // Update claim
            $warrantyClaim->update([
                'replaced_by' => auth()->id(),
                'replacement_product_id' => $replacementProduct->id,
                'replaced_at' => now(),
                'status' => 'completed'
            ]);

            // Update replacement product - sold with no warranty
            $replacementProduct->update([
                'status' => 'sold',
                'warranty_expires_at' => null
            ]);

            // Update old product
            $warrantyClaim->product->update([
                'status' => 'defective'
            ]);
        });

        return response()->json(['success' => true, 'message' => 'Replacement completed successfully']);
    }
}

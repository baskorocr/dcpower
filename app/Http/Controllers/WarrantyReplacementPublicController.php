<?php

namespace App\Http\Controllers;

use App\Models\Retail;
use App\Models\WarrantyClaim;
use App\Models\Product;
use Illuminate\Http\Request;

class WarrantyReplacementPublicController extends Controller
{
    public function login()
    {
        if (session('retail_id')) {
            return redirect()->route('warranty.replacement.index');
        }
        return view('warranty-replacement-login');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'pin' => 'required|string|size:6'
        ]);

        $retail = Retail::where('pin', $request->pin)
            ->where('phone', $request->phone)
            ->where('status', 'active')
            ->first();

        if (!$retail) {
            return back()->withInput()->with('error', 'Nomor telepon atau PIN tidak valid, atau retail tidak aktif');
        }

        session(['retail_id' => $retail->id, 'retail_name' => $retail->name]);

        return redirect()->route('warranty.replacement.index');
    }

    public function index()
    {
        if (!session('retail_id')) {
            return redirect()->route('warranty.replacement.login');
        }

        $retailId = session('retail_id');
        $retail = Retail::find($retailId);

        // Get approved claims for products that were at this retail
        $claims = WarrantyClaim::with(['product.project', 'replacementProduct'])
            ->whereHas('product.traceLogs', function($q) use ($retail) {
                $q->where('event_type', 'stock_out_retail')
                  ->where('location', $retail->name);
            })
            ->where('status', 'approved')
            ->latest()
            ->get();

        return view('warranty-replacement-public', compact('claims', 'retail'));
    }

    public function show(WarrantyClaim $claim)
    {
        if (!session('retail_id')) {
            return redirect()->route('warranty.replacement.login');
        }

        return view('warranty-replacement-scan', compact('claim'));
    }

    public function scan(Request $request, WarrantyClaim $claim)
    {
        if (!session('retail_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized']);
        }

        $request->validate(['serial_number' => 'required|string']);

        $replacementProduct = Product::where('serial_number', $request->serial_number)
            ->whereIn('status', ['manufactured', 'in_distributor', 'at_retail'])
            ->first();

        if (!$replacementProduct) {
            return response()->json(['success' => false, 'message' => 'Product not found or already used']);
        }

        if ($replacementProduct->warranty_expires_at) {
            return response()->json(['success' => false, 'message' => 'Product already has warranty activated']);
        }

        $oldProduct = $claim->product;

        // Expire old product warranty
        $oldProduct->update([
            'warranty_expires_at' => now(),
            'status' => 'warranty_expired'
        ]);

        // Expire replacement product warranty
        $replacementProduct->update([
            'warranty_expires_at' => now(),
            'status' => 'product_claim'
        ]);

        // Update claim
        $claim->update([
            'replacement_product_id' => $replacementProduct->id,
            'status' => 'completed',
            'replaced_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Replacement completed successfully',
            'old_product' => $oldProduct->serial_number,
            'new_product' => $replacementProduct->serial_number
        ]);
    }

    public function logout()
    {
        session()->forget(['retail_id', 'retail_name']);
        return redirect()->route('warranty.replacement.login')->with('success', 'Logged out successfully');
    }
}

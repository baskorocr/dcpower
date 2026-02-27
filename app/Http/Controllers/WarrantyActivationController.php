<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Project;
use App\Models\Retail;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WarrantyActivationController extends Controller
{
    public function index()
    {
        if (!session('retail_activation_id')) {
            return redirect()->route('warranty.activation.login');
        }
        return view('warranty.activation');
    }

    public function login()
    {
        if (session('retail_activation_id')) {
            return redirect()->route('warranty.activation');
        }
        return view('warranty-activation-login');
    }

    public function verify(Request $request)
    {
        $request->validate(['pin' => 'required|string|size:6']);

        $retail = Retail::where('pin', $request->pin)->where('status', 'active')->first();

        if (!$retail) {
            return back()->with('error', 'PIN tidak valid atau retail tidak aktif');
        }

        session(['retail_activation_id' => $retail->id, 'retail_activation_name' => $retail->name]);

        return redirect()->route('warranty.activation');
    }

    public function activate(Request $request)
    {
        if (!session('retail_activation_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'serial_number' => 'required|string|exists:products,serial_number'
        ]);

        $product = Product::where('serial_number', $request->serial_number)->first();

        // Check if product is expired or claimed
        if ($product->status === 'warranty_expired') {
            return response()->json([
                'success' => false,
                'message' => 'Product warranty has expired. Cannot activate.'
            ], 400);
        }

        if ($product->status === 'product_claim') {
            return response()->json([
                'success' => false,
                'message' => 'Product has been used for warranty claim replacement. Cannot activate.'
            ], 400);
        }

        if ($product->status === 'claim_rejected') {
            return response()->json([
                'success' => false,
                'message' => 'Product claim was rejected. Cannot activate.'
            ], 400);
        }

        if ($product->warranty_expires_at) {
            return response()->json([
                'success' => false,
                'message' => 'Warranty sudah diaktifkan sebelumnya',
                'warranty_expires_at' => $product->warranty_expires_at->format('d M Y')
            ], 400);
        }

        // Check if product has retail stock
        if ($product->retail_stock <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak tersedia di retail'
            ], 400);
        }

        $project = $product->project;
        $warrantyMonths = $project->warranty_period ?? 12;
        $retail = Retail::find(session('retail_activation_id'));

        // Update product: reduce retail stock, set warranty expiry, change status to sold
        $product->retail_stock = max(0, $product->retail_stock - 1);
        $product->warranty_expires_at = Carbon::now()->addMonths($warrantyMonths);
        $product->status = 'sold';
        $product->save();

        // Create trace log
        \App\Models\ProductTraceLog::create([
            'product_id' => $product->id,
            'user_id' => null,
            'scanned_by' => null,
            'event_type' => 'warranty_activation',
            'action' => 'warranty_activation',
            'location' => $retail ? $retail->name : 'Retail',
            'notes' => "Warranty activated at retail: " . ($retail ? $retail->name : 'Retail') . ". Expires: " . $product->warranty_expires_at->format('d M Y'),
            'scanned_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Warranty berhasil diaktifkan',
            'data' => [
                'serial_number' => $product->serial_number,
                'project_name' => $project->name,
                'activated_at' => now()->format('d M Y H:i'),
                'warranty_expires_at' => $product->warranty_expires_at->format('d M Y')
            ]
        ]);
    }

    public function logout()
    {
        session()->forget(['retail_activation_id', 'retail_activation_name']);
        return redirect()->route('warranty.activation.login')->with('success', 'Logged out successfully');
    }
}

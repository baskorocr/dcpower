<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductTraceLog;
use Illuminate\Http\Request;

class QRScanController extends Controller
{
    public function index()
    {
        return view('qr-scan.index');
    }

    public function scan(Request $request)
    {
        $validated = $request->validate([
            'qr_code' => 'required|string',
            'event_type' => 'required|in:manufactured,shipped,received,sold,claimed',
            'location' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $product = Product::where('qr_code', $validated['qr_code'])->first();

        if (!$product) {
            return back()->with('error', 'Product not found');
        }

        // Create trace log
        ProductTraceLog::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'event_type' => $validated['event_type'],
            'location' => $validated['location'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'scanned_at' => now(),
        ]);

        // Update product status
        $product->update(['status' => $validated['event_type']]);

        return back()->with('success', 'QR scanned successfully for: ' . $product->name);
    }
}

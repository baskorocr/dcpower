<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WarrantyClaim;
use App\Models\Sale;
use Illuminate\Http\Request;

class WarrantyClaimController extends Controller
{
    public function index()
    {
        $query = WarrantyClaim::with(['product', 'claimedBy']);
        
        if (auth()->user()->hasRole('buyer')) {
            $query->where('claimed_by_user_id', auth()->id());
        } elseif (auth()->user()->hasRole('distributor')) {
            $query->where('distributor_id', auth()->user()->distributor->id);
        }
        
        $claims = $query->latest()->paginate(15);
        
        return view('admin.warranty-claims.index', compact('claims'));
    }

    public function create()
    {
        return view('admin.warranty-claims.create');
    }

    public function checkSerial(Request $request)
    {
        $serialNumber = $request->input('serial_number');
        
        $product = \App\Models\Product::where('serial_number', $serialNumber)->first();
        
        if (!$product) {
            return response()->json([
                'status' => 'unknown',
                'message' => 'Product Unknown - Serial number tidak ditemukan di database',
                'valid' => false
            ]);
        }

        if ($product->status === 'warranty_expired') {
            return response()->json([
                'status' => 'expired',
                'message' => 'Product Warranty Expired - Garansi produk sudah habis',
                'valid' => false,
                'product_status' => 'warranty_expired'
            ]);
        }

        if ($product->status === 'product_claim') {
            return response()->json([
                'status' => 'claimed',
                'message' => 'Product Already Claimed - Produk sudah digunakan untuk penggantian klaim',
                'valid' => false,
                'product_status' => 'product_claim'
            ]);
        }

        if ($product->status === 'claim_rejected') {
            return response()->json([
                'status' => 'rejected',
                'message' => 'Claim Rejected - Klaim produk ini sudah ditolak sebelumnya',
                'valid' => false,
                'product_status' => 'claim_rejected'
            ]);
        }
        
        if (!$product->warranty_expires_at) {
            return response()->json([
                'status' => 'not_activated',
                'message' => 'Product Not Activated - Warranty belum diaktivasi',
                'valid' => false,
                'product_status' => $product->status
            ]);
        }
        
        return response()->json([
            'status' => 'genuine',
            'message' => 'Product Original Genuine - Produk asli dan terverifikasi',
            'valid' => true,
            'product_status' => $product->status,
            'product' => [
                'name' => $product->project->name ?? 'N/A',
                'activated_at' => $product->warranty_activated_at?->format('d/m/Y'),
                'expires_at' => $product->warranty_expires_at?->format('d/m/Y')
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'serial_number' => 'required|string',
            'complaint_type' => 'required|in:defect,damage,malfunction,other',
            'complaint_description' => 'required|string',
            'photo_evidence' => 'required|image|mimes:jpeg,jpg,png|max:5120|dimensions:min_width=400,min_height=400',
            'motor_type' => 'required|string|max:255',
            'has_modification' => 'required|boolean',
            'modification_types' => 'nullable|array',
            'modification_types.*' => 'in:boreup,ganti_kiprok,ganti_spull,ganti_coil',
            'whatsapp_number' => 'required|string|max:20',
            'purchase_type' => 'required|in:online,offline',
            'purchase_date' => 'required|date|before_or_equal:today',
            'battery_issue_date' => 'required|date|after_or_equal:purchase_date|before_or_equal:today',
        ]);

        // Find product by serial number
        $product = \App\Models\Product::where('serial_number', $validated['serial_number'])
            ->whereNotNull('warranty_expires_at')
            ->whereDate('warranty_expires_at', '>=', now())
            ->whereNotIn('status', ['warranty_expired', 'product_claim', 'claim_rejected'])
            ->first();

        if (!$product) {
            return back()->withErrors(['serial_number' => 'Serial number not found, warranty has expired, product already used for claim replacement, or claim was rejected.'])->withInput();
        }

        // Upload photo with strict validation
        if ($request->hasFile('photo_evidence')) {
            $file = $request->file('photo_evidence');
            
            // Additional MIME type check
            $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!in_array($file->getMimeType(), $allowedMimes)) {
                return back()->withErrors(['photo_evidence' => 'Invalid file type. Only JPEG and PNG images are allowed.']);
            }
            
            // Check if file is actually an image
            if (!@getimagesize($file->getRealPath())) {
                return back()->withErrors(['photo_evidence' => 'File is not a valid image.']);
            }
            
            $photoPath = $file->store('warranty-claims', 'public');
        }

        $claim = WarrantyClaim::create([
            'product_id' => $product->id,
            'complaint_type' => $validated['complaint_type'],
            'complaint_description' => $validated['complaint_description'],
            'claimed_by_user_id' => auth()->id(),
            'photo_evidence' => $photoPath ?? null,
            'status' => 'pending',
            'submitted_at' => now(),
            'motor_type' => $validated['motor_type'],
            'has_modification' => $validated['has_modification'],
            'modification_types' => $validated['has_modification'] ? $validated['modification_types'] : null,
            'whatsapp_number' => $validated['whatsapp_number'],
            'purchase_type' => $validated['purchase_type'],
            'purchase_date' => $validated['purchase_date'],
            'battery_issue_date' => $validated['battery_issue_date'],
        ]);

        // Log history
        $claim->histories()->create([
            'actor_user_id' => auth()->id(),
            'new_status' => 'pending',
            'notes' => 'Claim submitted',
            'acted_at' => now(),
        ]);

        return redirect()->route('warranty-claims.index')->with('success', 'Claim submitted: ' . $claim->claim_number);
    }

    public function show(WarrantyClaim $warrantyClaim)
    {
        $warrantyClaim->load([
            'product.stockMovements.distributor',
            'product.stockMovements.retail',
            'product.traceLogs',
            'histories.actor'
        ]);
        return view('admin.warranty-claims.show', compact('warrantyClaim'));
    }

    public function update(Request $request, WarrantyClaim $warrantyClaim)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected,completed',
            'resolution_notes' => 'nullable|string',
        ]);

        $oldStatus = $warrantyClaim->status;
        $warrantyClaim->update([
            ...$validated,
            'handled_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        // Log history
        $warrantyClaim->histories()->create([
            'actor_user_id' => auth()->id(),
            'old_status' => $oldStatus,
            'new_status' => $validated['status'],
            'notes' => $validated['resolution_notes'] ?? 'Status updated',
            'acted_at' => now(),
        ]);

        return redirect()->route('warranty-claims.show', $warrantyClaim)->with('success', 'Claim updated');
    }
}

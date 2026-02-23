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
        $query = WarrantyClaim::with(['product', 'sale', 'claimedBy']);
        
        if (auth()->user()->hasRole('buyer')) {
            $query->where('claimed_by_user_id', auth()->id());
        } elseif (auth()->user()->hasRole('distributor')) {
            $query->whereHas('sale', function($q) {
                $q->where('distributor_id', auth()->user()->distributor->id);
            });
        }
        
        $claims = $query->latest()->paginate(15);
        
        return view('admin.warranty-claims.index', compact('claims'));
    }

    public function create()
    {
        return view('admin.warranty-claims.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'serial_number' => 'required|string',
            'complaint_type' => 'required|in:defect,damage,malfunction,other',
            'complaint_description' => 'required|string',
            'photo_evidence' => 'required|image|mimes:jpeg,jpg,png|max:5120|dimensions:min_width=400,min_height=400',
        ]);

        // Find sale by serial number
        $sale = Sale::whereHas('product', function($q) use ($validated) {
            $q->where('serial_number', $validated['serial_number']);
        })
        ->where('buyer_user_id', auth()->id())
        ->whereDate('warranty_end', '>=', now())
        ->with('product')
        ->first();

        if (!$sale) {
            return back()->withErrors(['serial_number' => 'Serial number not found or warranty has expired.'])->withInput();
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
            'sale_id' => $sale->id,
            'complaint_type' => $validated['complaint_type'],
            'complaint_description' => $validated['complaint_description'],
            'product_id' => $sale->product_id,
            'claimed_by_user_id' => auth()->id(),
            'distributor_id' => $sale->distributor_id,
            'photo_evidence' => $photoPath ?? null,
            'status' => 'pending',
            'submitted_at' => now(),
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
        $warrantyClaim->load(['product', 'sale', 'histories.actor']);
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

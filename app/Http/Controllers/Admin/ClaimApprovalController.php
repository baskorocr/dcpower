<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WarrantyClaim;
use Illuminate\Http\Request;

class ClaimApprovalController extends Controller
{
    public function index()
    {
        $claims = WarrantyClaim::with(['product', 'claimedBy'])
            ->whereIn('status', ['pending', 'under_review'])
            ->latest()
            ->paginate(20);
        
        return view('admin.claim-approvals.index', compact('claims'));
    }

    public function show(WarrantyClaim $claim)
    {
        $claim->load(['product', 'claimedBy', 'histories']);
        return view('admin.claim-approvals.show', compact('claim'));
    }

    public function approve(Request $request, WarrantyClaim $claim)
    {
        $request->validate([
            'resolution_notes' => 'required|string',
        ]);

        $claim->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'resolution_notes' => $request->resolution_notes,
        ]);

        $claim->histories()->create([
            'actor_user_id' => auth()->id(),
            'new_status' => 'approved',
            'notes' => $request->resolution_notes,
            'acted_at' => now(),
        ]);

        return redirect()->route('claim-approvals.index')->with('success', 'Claim approved successfully');
    }

    public function reject(Request $request, WarrantyClaim $claim)
    {
        $request->validate([
            'resolution_notes' => 'required|string',
        ]);

        $claim->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'resolution_notes' => $request->resolution_notes,
        ]);

        // Update product status to claim_rejected
        $claim->product->update([
            'status' => 'claim_rejected'
        ]);

        $claim->histories()->create([
            'actor_user_id' => auth()->id(),
            'new_status' => 'rejected',
            'notes' => $request->resolution_notes,
            'acted_at' => now(),
        ]);

        return redirect()->route('claim-approvals.index')->with('success', 'Claim rejected');
    }
}

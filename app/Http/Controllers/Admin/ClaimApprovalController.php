<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WarrantyClaim;
use Illuminate\Http\Request;

class ClaimApprovalController extends Controller
{
    public function index()
    {
        $claims = WarrantyClaim::with(['product', 'claimedBy', 'sale'])
            ->whereIn('status', ['pending', 'under_review'])
            ->latest()
            ->paginate(20);
        
        return view('admin.claim-approvals.index', compact('claims'));
    }

    public function show(WarrantyClaim $claim)
    {
        $claim->load(['product', 'claimedBy', 'sale', 'histories']);
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
            'user_id' => auth()->id(),
            'action' => 'approved',
            'notes' => $request->resolution_notes,
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

        $claim->histories()->create([
            'user_id' => auth()->id(),
            'action' => 'rejected',
            'notes' => $request->resolution_notes,
        ]);

        return redirect()->route('claim-approvals.index')->with('success', 'Claim rejected');
    }
}

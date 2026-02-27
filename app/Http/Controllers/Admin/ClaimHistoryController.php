<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WarrantyClaim;
use Illuminate\Http\Request;

class ClaimHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = WarrantyClaim::with(['product.project', 'claimedBy', 'approver']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('submitted_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('submitted_at', '<=', $request->date_to);
        }

        // Search by claim number or serial number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('claim_number', 'like', "%{$search}%")
                  ->orWhereHas('product', function($q2) use ($search) {
                      $q2->where('serial_number', 'like', "%{$search}%");
                  });
            });
        }

        $claims = $query->latest('submitted_at')->paginate(20);

        return view('admin.claim-history.index', compact('claims'));
    }
}

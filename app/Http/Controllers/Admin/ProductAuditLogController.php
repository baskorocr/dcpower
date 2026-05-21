<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductAuditLog;
use Illuminate\Http\Request;

class ProductAuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductAuditLog::with(['product.project', 'user']);

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('serial_number', 'like', "%{$search}%");
            });
        }

        $logs = $query->latest()->paginate(50);
        $users = \App\Models\User::orderBy('name')->get();

        return view('admin.product-audit-logs.index', compact('logs', 'users'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StandardPacking;
use App\Models\Project;
use Illuminate\Http\Request;

class StandardPackingController extends Controller
{
    public function index(Request $request)
    {
        $query = StandardPacking::with(['project', 'creator', 'products']);
        
        // Get user's projects
        if (!auth()->user()->hasRole('admin')) {
            $projectIds = auth()->user()->projects->pluck('id');
            $query->whereIn('project_id', $projectIds);
            $projects = auth()->user()->projects;
        } else {
            $projects = Project::all();
        }
        
        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Search by packing code
        if ($request->filled('search')) {
            $query->where('packing_code', 'like', '%' . $request->search . '%');
        }
        
        $packings = $query->latest()->paginate(20)->withQueryString();
        
        return view('admin.standard-packings.index', compact('packings', 'projects'));
    }

    public function show(StandardPacking $standardPacking)
    {
        $standardPacking->load(['project', 'creator', 'products.traceLogs']);
        return view('admin.standard-packings.show', compact('standardPacking'));
    }

    public function print(StandardPacking $standardPacking)
    {
        return view('admin.standard-packings.print', compact('standardPacking'));
    }
}

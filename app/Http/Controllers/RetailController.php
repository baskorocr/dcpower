<?php

namespace App\Http\Controllers;

use App\Models\Retail;
use App\Models\Distributor;
use Illuminate\Http\Request;

class RetailController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('admin');
        
        $query = Retail::with('distributor.project');
        
        if ($user->hasRole('distributor')) {
            $distributor = Distributor::where('user_id', $user->id)->first();
            if ($distributor) {
                $query->where('distributor_id', $distributor->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif (!$isAdmin) {
            $projectIds = $user->projects->pluck('id');
            $query->whereHas('distributor', fn($q) => $q->whereIn('project_id', $projectIds));
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('project_id')) {
            $query->whereHas('distributor', fn($q) => $q->where('project_id', $request->project_id));
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $retails = $query->latest()->paginate(20);
        $projects = $isAdmin ? \App\Models\Project::all() : collect();
        
        return view('retails.index', compact('retails', 'projects', 'isAdmin'));
    }

    public function create()
    {
        $user = auth()->user();
        
        if ($user->hasRole('distributor')) {
            $distributor = Distributor::where('user_id', $user->id)->first();
            $distributors = $distributor ? collect([$distributor]) : collect([]);
        } elseif ($user->hasRole('admin')) {
            $distributors = Distributor::where('status', 'active')->get();
        } else {
            $projectIds = $user->projects->pluck('id');
            $distributors = Distributor::whereIn('project_id', $projectIds)->where('status', 'active')->get();
        }
        
        return view('retails.create', compact('distributors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'distributor_id' => 'required|exists:distributors,id',
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'pin' => 'required|string|size:6',
            'status' => 'required|in:active,inactive',
        ]);

        Retail::create($validated);

        return redirect()->route('retails.index')->with('success', 'Retail created successfully');
    }

    public function edit(Retail $retail)
    {
        $user = auth()->user();
        
        if ($user->hasRole('distributor')) {
            $distributor = Distributor::where('user_id', $user->id)->first();
            if (!$distributor || $retail->distributor_id != $distributor->id) {
                abort(403);
            }
            $distributors = collect([$distributor]);
        } elseif ($user->hasRole('admin')) {
            $distributors = Distributor::where('status', 'active')->get();
        } else {
            $projectIds = $user->projects->pluck('id');
            $distributors = Distributor::whereIn('project_id', $projectIds)->where('status', 'active')->get();
        }
        
        return view('retails.edit', compact('retail', 'distributors'));
    }

    public function update(Request $request, Retail $retail)
    {
        $validated = $request->validate([
            'distributor_id' => 'required|exists:distributors,id',
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'pin' => 'required|string|size:6',
            'status' => 'required|in:active,inactive',
        ]);

        $retail->update($validated);

        return redirect()->route('retails.index')->with('success', 'Retail updated successfully');
    }

    public function destroy(Retail $retail)
    {
        $user = auth()->user();
        
        // Only distributor, PM, marketing, and admin can delete
        if ($user->hasRole('distributor')) {
            $distributor = Distributor::where('user_id', $user->id)->first();
            if (!$distributor || $retail->distributor_id != $distributor->id) {
                abort(403);
            }
        } elseif (!$user->hasAnyRole(['PM', 'Marketing', 'admin'])) {
            abort(403);
        }
        
        $retail->delete();
        return redirect()->route('retails.index')->with('success', 'Retail deleted successfully');
    }

    public function bulkDelete(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->hasAnyRole(['PM', 'Marketing', 'admin'])) {
            abort(403);
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:retails,id'
        ]);

        Retail::whereIn('id', $request->ids)->delete();

        return redirect()->route('retails.index')->with('success', count($request->ids) . ' retails deleted successfully');
    }
}

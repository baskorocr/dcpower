<?php

namespace App\Http\Controllers;

use App\Models\Distributor;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DistributorController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('admin');
        
        $query = Distributor::with(['project', 'user']);
        
        if (!$isAdmin) {
            $projectIds = \DB::table('project_users')->where('user_id', $user->id)->pluck('project_id');
            $query->whereIn('project_id', $projectIds);
        } else {
            if ($request->filled('project_id')) {
                $query->where('project_id', $request->project_id);
            }
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $distributors = $query->latest()->paginate(20);
        $projects = $isAdmin ? Project::all() : collect();
        
        return view('distributors.index', compact('distributors', 'projects', 'isAdmin'));
    }

    public function create()
    {
        $user = auth()->user();
        $projects = $user->hasRole('admin') 
            ? Project::all() 
            : Project::whereIn('id', \DB::table('project_users')->where('user_id', $user->id)->pluck('project_id'))->get();
        
        return view('distributors.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'province' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        if (!auth()->user()->hasRole('admin')) {
            $allowedProjectIds = \DB::table('project_users')
                ->where('user_id', auth()->id())
                ->pluck('project_id')
                ->toArray();
            
            if (!in_array($validated['project_id'], $allowedProjectIds)) {
                abort(403, 'Unauthorized project access');
            }
        }

        // Create user account for distributor
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        $user->assignRole('distributor');

        // Create distributor
        $distributor = Distributor::create([
            'project_id' => $validated['project_id'],
            'user_id' => $user->id,
            'code' => 'DIST-' . strtoupper(substr(uniqid(), -8)),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'province' => $validated['province'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'status' => 'active',
        ]);

        return redirect()->route('distributors.index')->with('success', 'Distributor created successfully');
    }

    public function show(Distributor $distributor)
    {
        $distributor->load(['project', 'user', 'stockMovements']);
        $stockCount = $distributor->stockMovements()
            ->selectRaw('SUM(CASE WHEN type = "in" THEN quantity ELSE -quantity END) as total')
            ->value('total') ?? 0;
        
        return view('distributors.show', compact('distributor', 'stockCount'));
    }

    public function edit(Distributor $distributor)
    {
        $user = auth()->user();
        $projects = $user->hasRole('admin') 
            ? Project::all() 
            : Project::whereIn('id', \DB::table('project_users')->where('user_id', $user->id)->pluck('project_id'))->get();
        
        return view('distributors.edit', compact('distributor', 'projects'));
    }

    public function update(Request $request, Distributor $distributor)
    {
        try {
            $validated = $request->validate([
                'project_id' => 'required|exists:projects,id',
                'name' => 'required|string|max:255',
                'phone' => 'nullable|string',
                'address' => 'nullable|string',
                'city' => 'nullable|string',
                'province' => 'nullable|string',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'status' => 'required|in:active,inactive',
            ]);

            if (!auth()->user()->hasRole('admin')) {
                $allowedProjectIds = \DB::table('project_users')
                    ->where('user_id', auth()->id())
                    ->pluck('project_id')
                    ->toArray();
                
                if (!in_array($validated['project_id'], $allowedProjectIds)) {
                    return redirect()->back()->with('error', 'Unauthorized project access')->withInput();
                }
            }

            $distributor->update($validated);
            
            if ($distributor->user) {
                $distributor->user->update(['name' => $validated['name']]);
            }

            return redirect()->route('distributors.index')->with('success', 'Distributor updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update distributor: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Distributor $distributor)
    {
        $user = auth()->user();
        
        // Only PM, marketing, and admin can delete distributors
        if (!$user->hasAnyRole(['PM', 'Marketing', 'admin'])) {
            abort(403);
        }
        
        $distributor->delete();
        return redirect()->route('distributors.index')->with('success', 'Distributor deleted successfully');
    }

    public function bulkDelete(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->hasAnyRole(['PM', 'Marketing', 'admin'])) {
            abort(403);
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:distributors,id'
        ]);

        Distributor::whereIn('id', $request->ids)->delete();

        return redirect()->route('distributors.index')->with('success', count($request->ids) . ' distributors deleted successfully');
    }
}

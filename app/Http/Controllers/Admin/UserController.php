<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles', 'projects', 'distributor.project');
        
        // Filter by role
        if ($request->filled('role')) {
            $query->role($request->role);
        }
        
        // Search by name or email
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        if (!auth()->user()->hasRole('admin')) {
            $projectIds = auth()->user()->projects->pluck('id');
            $query->whereHas('projects', function($q) use ($projectIds) {
                $q->whereIn('projects.id', $projectIds);
            });
        }
        
        $users = $query->latest()->paginate(15)->withQueryString();
        $roles = Role::all();
        
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        if (auth()->user()->hasRole('admin')) {
            $roles = Role::all();
            $projects = \App\Models\Project::where('status', 'active')->get();
        } else {
            $roles = Role::where('name', '!=', 'admin')->get();
            $projects = auth()->user()->projects()->where('status', 'active')->get();
        }
        
        return view('admin.users.create', compact('roles', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string',
            'roles' => 'required|array',
            'projects' => 'nullable|array',
        ]);

        if (!auth()->user()->hasRole('admin')) {
            $validated['roles'] = array_filter($validated['roles'], fn($role) => $role !== 'admin');
            
            if (!empty($validated['projects'])) {
                $allowedProjectIds = auth()->user()->projects->pluck('id')->toArray();
                $validated['projects'] = array_intersect($validated['projects'], $allowedProjectIds);
            }
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'phone' => $validated['phone'],
        ]);

        $user->syncRoles($validated['roles']);
        
        // Only sync projects if user is not a buyer or if projects are provided
        $isBuyer = in_array('buyer', $validated['roles']);
        if (!$isBuyer && !empty($validated['projects'])) {
            $user->projects()->sync($validated['projects']);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    public function edit(User $user)
    {
        if (!auth()->user()->hasRole('admin')) {
            $projectIds = auth()->user()->projects->pluck('id');
            $userProjectIds = $user->projects->pluck('id');
            
            if ($projectIds->intersect($userProjectIds)->isEmpty()) {
                abort(403, 'Unauthorized access');
            }
            
            $roles = Role::where('name', '!=', 'admin')->get();
            $projects = auth()->user()->projects()->where('status', 'active')->get();
        } else {
            $roles = Role::all();
            $projects = \App\Models\Project::where('status', 'active')->get();
        }
        
        return view('admin.users.edit', compact('user', 'roles', 'projects'));
    }

    public function update(Request $request, User $user)
    {
        if (!auth()->user()->hasRole('admin')) {
            $projectIds = auth()->user()->projects->pluck('id');
            $userProjectIds = $user->projects->pluck('id');
            
            if ($projectIds->intersect($userProjectIds)->isEmpty()) {
                abort(403, 'Unauthorized access');
            }
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'required|array',
            'projects' => 'nullable|array',
        ]);

        if (!auth()->user()->hasRole('admin')) {
            $validated['roles'] = array_filter($validated['roles'], fn($role) => $role !== 'admin');
            
            if (!empty($validated['projects'])) {
                $allowedProjectIds = auth()->user()->projects->pluck('id')->toArray();
                $validated['projects'] = array_intersect($validated['projects'], $allowedProjectIds);
            }
        }

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ];

        // Only update password if provided
        if (!empty($validated['password'])) {
            $updateData['password'] = bcrypt($validated['password']);
        }

        $user->update($updateData);

        $user->syncRoles($validated['roles']);
        $user->projects()->sync($validated['projects'] ?? []);

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }
}

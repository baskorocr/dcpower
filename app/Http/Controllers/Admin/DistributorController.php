<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Distributor;
use App\Models\User;
use Illuminate\Http\Request;

class DistributorController extends Controller
{
    public function index()
    {
        $projectId = auth()->user()->hasRole('Admin') 
            ? request('project_id') 
            : auth()->user()->projectUsers()->first()?->project_id;

        $distributors = Distributor::with(['project', 'user'])
            ->when($projectId, fn($q) => $q->where('project_id', $projectId))
            ->latest()
            ->paginate(10);

        return view('admin.distributors.index', compact('distributors'));
    }

    public function create()
    {
        $projectId = auth()->user()->hasRole('Admin') 
            ? null 
            : auth()->user()->projectUsers()->first()?->project_id;

        $users = User::role('Distributor')->get();

        return view('admin.distributors.create', compact('users', 'projectId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'user_id' => 'nullable|exists:users,id',
            'code' => 'required|unique:distributors,code',
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'status' => 'required|in:active,inactive'
        ]);

        Distributor::create($validated);

        return redirect()->route('distributors.index')->with('success', 'Distributor created successfully');
    }

    public function edit(Distributor $distributor)
    {
        $users = User::role('Distributor')->get();
        return view('admin.distributors.edit', compact('distributor', 'users'));
    }

    public function update(Request $request, Distributor $distributor)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'code' => 'required|unique:distributors,code,' . $distributor->id,
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'status' => 'required|in:active,inactive'
        ]);

        $distributor->update($validated);

        return redirect()->route('distributors.index')->with('success', 'Distributor updated successfully');
    }

    public function destroy(Distributor $distributor)
    {
        $distributor->delete();
        return redirect()->route('distributors.index')->with('success', 'Distributor deleted successfully');
    }
}

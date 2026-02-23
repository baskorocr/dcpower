<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = auth()->user()->hasRole('admin') 
            ? Project::with('creator')->latest()->paginate(10)
            : auth()->user()->projects()->with('creator')->latest()->paginate(10);
        
        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('admin.projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'project_code' => 'required|string|max:50',
            'description' => 'nullable|string',
            'warranty_duration' => 'required|integer|min:1',
            'standard_packing_quantity' => 'nullable|integer|min:1',
            'use_variants' => 'nullable|boolean',
            'variants' => 'nullable|array',
            'variants.*' => 'required|string|max:50',
            'packing_format' => 'nullable|string|max:255',
        ]);

        $validated['use_variants'] = $request->has('use_variants');
        $validated['project_code'] = strtoupper($validated['project_code']);
        
        $project = Project::create([
            ...$validated,
            'created_by_user_id' => auth()->id(),
        ]);

        return redirect()->route('projects.index')->with('success', 'Project created successfully');
    }

    public function show(Project $project)
    {
        return view('admin.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        return view('admin.projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'warranty_duration' => 'required|integer|min:1',
            'standard_packing_quantity' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        $project->update($validated);

        return redirect()->route('projects.index')->with('success', 'Project updated successfully');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully');
    }
}

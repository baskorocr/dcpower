<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array',
        ]);

        $role = Role::create(['name' => $validated['name']]);
        
        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return back()->with('success', 'Role created successfully');
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => 'required|array',
        ]);

        $role->syncPermissions($validated['permissions']);

        return back()->with('success', 'Role permissions updated');
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, ['admin', 'project-manager', 'qa', 'distributor', 'buyer'])) {
            return back()->with('error', 'Cannot delete system role');
        }

        $role->delete();
        return back()->with('success', 'Role deleted successfully');
    }

    public function storePermission(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name',
        ]);

        Permission::create(['name' => $validated['name']]);

        return back()->with('success', 'Permission created successfully');
    }
}

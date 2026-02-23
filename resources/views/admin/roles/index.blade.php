<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Roles & Permissions</h2>
            <div class="flex gap-2">
                <button onclick="document.getElementById('addPermissionModal').classList.remove('hidden')" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                    + Add Permission
                </button>
                <button onclick="document.getElementById('addRoleModal').classList.remove('hidden')" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    + Add Role
                </button>
            </div>
        </div>
    </x-slot>

    <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl border-2 border-emerald-100 dark:border-emerald-800">
        @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-50 dark:bg-emerald-900/20 border-l-4 border-emerald-500 text-emerald-700 dark:text-emerald-400">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-400">
            {{ session('error') }}
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($roles as $role)
            <div class="p-4 border-2 border-emerald-100 dark:border-emerald-800 rounded-xl">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ ucfirst($role->name) }}</h3>
                    @if(!in_array($role->name, ['admin', 'project-manager', 'qa', 'distributor', 'buyer']))
                    <form method="POST" action="{{ route('roles.destroy', $role) }}" onsubmit="return confirm('Delete this role?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm">Delete</button>
                    </form>
                    @endif
                </div>
                
                <form method="POST" action="{{ route('roles.update', $role) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-2 mb-4">
                        @foreach($permissions as $permission)
                        <label class="flex items-center text-sm">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">{{ $permission->name }}</span>
                        </label>
                        @endforeach
                    </div>

                    <button type="submit" class="w-full py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-sm font-bold rounded-lg hover:scale-105 transition-transform">
                        Update Permissions
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Add Role Modal -->
    <div id="addRoleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Add New Role</h3>
                <button onclick="document.getElementById('addRoleModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">✕</button>
            </div>

            <form method="POST" action="{{ route('roles.store') }}">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Role Name *</label>
                    <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-200">
                    @error('name')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Permissions</label>
                    <div class="space-y-2 max-h-60 overflow-y-auto">
                        @foreach($permissions as $permission)
                        <label class="flex items-center text-sm">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                            <span class="ml-2 text-gray-700 dark:text-gray-300">{{ $permission->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="flex-1 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Create Role</button>
                    <button type="button" onclick="document.getElementById('addRoleModal').classList.add('hidden')" class="flex-1 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Add Permission Modal -->
    <div id="addPermissionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Add New Permission</h3>
                <button onclick="document.getElementById('addPermissionModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">✕</button>
            </div>

            <form method="POST" action="{{ route('permissions.store') }}">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Permission Name *</label>
                    <input type="text" name="name" required placeholder="e.g., manage-products" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-200">
                    @error('name')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="flex-1 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">Create Permission</button>
                    <button type="button" onclick="document.getElementById('addPermissionModal').classList.add('hidden')" class="flex-1 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

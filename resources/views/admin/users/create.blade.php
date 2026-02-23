<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Create New User</h2>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl border-2 border-emerald-100 dark:border-emerald-800">
            <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Password *</label>
                    <input type="password" name="password" required class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2">
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Roles *</label>
                    <div class="space-y-2 p-3 bg-gray-50 dark:bg-dark-eval-2 rounded-lg">
                        @foreach($roles as $role)
                        <label class="flex items-center">
                            <input type="checkbox" name="roles[]" value="{{ $role->name }}" {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }} class="role-checkbox w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500" data-role="{{ $role->name }}">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('roles')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div id="project-section">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Assign to Projects</label>
                    <div class="space-y-2 p-3 bg-gray-50 dark:bg-dark-eval-2 rounded-lg max-h-48 overflow-y-auto">
                        @forelse($projects as $project)
                        <label class="flex items-center">
                            <input type="checkbox" name="projects[]" value="{{ $project->id }}" {{ in_array($project->id, old('projects', [])) ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $project->name }}</span>
                        </label>
                        @empty
                        <p class="text-sm text-gray-500">No projects available</p>
                        @endforelse
                    </div>
                    <p class="text-xs text-gray-500 mt-1">User will only see data from assigned projects (not required for Buyer)</p>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const roleCheckboxes = document.querySelectorAll('.role-checkbox');
                        const projectSection = document.getElementById('project-section');
                        
                        function toggleProjectSection() {
                            const buyerChecked = Array.from(roleCheckboxes).some(cb => 
                                cb.checked && cb.dataset.role === 'buyer'
                            );
                            
                            if (buyerChecked) {
                                projectSection.style.display = 'none';
                            } else {
                                projectSection.style.display = 'block';
                            }
                        }
                        
                        roleCheckboxes.forEach(cb => {
                            cb.addEventListener('change', toggleProjectSection);
                        });
                        
                        toggleProjectSection();
                    });
                </script>

                <div class="flex gap-2">
                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold rounded-lg hover:scale-105 transition-transform">
                        Create User
                    </button>
                    <a href="{{ route('users.index') }}" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

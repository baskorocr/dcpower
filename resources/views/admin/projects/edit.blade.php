<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Edit Project</h2>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl border-2 border-emerald-100 dark:border-emerald-800">
            <form method="POST" action="{{ route('projects.update', $project) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Project Name *</label>
                    <input type="text" name="name" value="{{ old('name', $project->name) }}" required class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Project Code *</label>
                    <input type="text" name="project_code" value="{{ old('project_code', $project->project_code) }}" required placeholder="e.g., TUS, DCPOWER" class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2 uppercase" readonly disabled>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Project code cannot be changed</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2">{{ old('description', $project->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Warranty Duration (months) *</label>
                    <input type="number" name="warranty_duration" value="{{ old('warranty_duration', $project->warranty_duration) }}" required min="1" class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2">
                    @error('warranty_duration')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Standard Packing Quantity</label>
                    <input type="number" name="standard_packing_quantity" value="{{ old('standard_packing_quantity', $project->standard_packing_quantity) }}" min="1" placeholder="Leave empty if not using standard packing" class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Number of products per standard packing (e.g., 10). Leave empty if project doesn't use standard packing.</p>
                    @error('standard_packing_quantity')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Use Product Variants</label>
                    <input type="checkbox" disabled {{ $project->use_variants ? 'checked' : '' }} class="rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500">
                    <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">Cannot be changed after creation</span>
                    @if($project->use_variants && $project->variants)
                    <div class="mt-2 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Variants:</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ implode(', ', $project->variants) }}</p>
                    </div>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Packing Code Format</label>
                    <input type="text" name="packing_format" value="{{ old('packing_format', $project->packing_format) }}" class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2 font-mono text-sm">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Available: {PROJECT_NAME}, {PROJECT_CODE}, {VARIANT}, {YYYY}, {MM}, {DD}, {YYYYMMDD}, {BATCH:5}, {RANDOM}<br>
                        Example: TUS-400-20260218-00001
                    </p>
                    @error('packing_format')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Status *</label>
                    <select name="status" required class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2">
                        <option value="active" {{ old('status', $project->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $project->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold rounded-lg hover:scale-105 transition-transform">
                        Update Project
                    </button>
                    <a href="{{ route('projects.index') }}" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Create New Project</h2>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl border-2 border-emerald-100 dark:border-emerald-800">
            <form method="POST" action="{{ route('projects.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Project Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Project Code *</label>
                    <input type="text" name="project_code" value="{{ old('project_code') }}" required placeholder="e.g., TUS, DCPOWER" class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2 uppercase">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">This will be used in packing code format (e.g., TUS-200-20260218-00001)</p>
                    @error('project_code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Warranty Duration (months) *</label>
                    <input type="number" name="warranty_duration" value="{{ old('warranty_duration', 12) }}" required min="1" class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2">
                    @error('warranty_duration')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Standard Packing Quantity</label>
                    <input type="number" name="standard_packing_quantity" value="{{ old('standard_packing_quantity') }}" min="1" placeholder="Leave empty if not using standard packing" class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Number of products per standard packing (e.g., 10). Leave empty if project doesn't use standard packing.</p>
                    @error('standard_packing_quantity')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="flex items-center space-x-2 mb-2">
                        <input type="checkbox" name="use_variants" id="use_variants" value="1" {{ old('use_variants') ? 'checked' : '' }} class="rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Use Product Variants</span>
                    </label>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Enable if this project has multiple product variants (e.g., 7000, 8000, 9000)</p>
                </div>

                <div id="variants-section" class="hidden space-y-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Number of Variants</label>
                        <input type="number" id="variant_count" min="1" max="20" placeholder="e.g., 3" class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2">
                    </div>
                    <div id="variants-list" class="space-y-2"></div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Packing Code Format</label>
                    <input type="text" name="packing_format" value="{{ old('packing_format', '{PROJECT_NAME}-{VARIANT}-{YYYYMMDD}-{BATCH:5}') }}" class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2 font-mono text-sm">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Available: {PROJECT_NAME}, {PROJECT_CODE}, {VARIANT}, {YYYY}, {MM}, {DD}, {YYYYMMDD}, {BATCH:5}, {RANDOM}<br>
                        Example: TUS-400-20260218-00001
                    </p>
                    @error('packing_format')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold rounded-lg hover:scale-105 transition-transform">
                        Create Project
                    </button>
                    <a href="{{ route('projects.index') }}" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const useVariants = document.getElementById('use_variants');
        const variantsSection = document.getElementById('variants-section');
        const variantCount = document.getElementById('variant_count');
        const variantsList = document.getElementById('variants-list');

        useVariants.addEventListener('change', function() {
            if (this.checked) {
                variantsSection.classList.remove('hidden');
            } else {
                variantsSection.classList.add('hidden');
                variantsList.innerHTML = '';
            }
        });

        variantCount.addEventListener('input', function() {
            const count = parseInt(this.value) || 0;
            variantsList.innerHTML = '';
            
            for (let i = 0; i < count; i++) {
                const div = document.createElement('div');
                div.innerHTML = `
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Variant ${i + 1}</label>
                    <input type="text" name="variants[]" placeholder="e.g., 7000" required class="w-full px-3 py-2 border border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2 text-sm">
                `;
                variantsList.appendChild(div);
            }
        });

        // Trigger on load if checked
        if (useVariants.checked) {
            variantsSection.classList.remove('hidden');
        }
    </script>
</x-app-layout>

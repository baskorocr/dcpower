<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Distributors</h2>
            <div class="flex gap-2">
                @php
                    $canDelete = false;
                    $user = auth()->user();
                    if ($user->hasAnyRole(['Marketing', 'admin', 'PM'])) {
                        $canDelete = true;
                    }
                @endphp
                @if($canDelete)
                <button type="button" onclick="deleteSelected()" id="deleteBtn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 hidden">
                    Delete Selected
                </button>
                @endif
                @can('manage-distributors')
                <a href="{{ route('distributors.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    + Add Distributor
                </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        <!-- Filters -->
        <div class="p-4 bg-white dark:bg-dark-eval-1 rounded-2xl">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search code, name, city..." class="px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg dark:bg-dark-eval-2 text-sm">
                
                @if($isAdmin)
                <select name="project_id" class="px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg dark:bg-dark-eval-2 text-sm">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                    @endforeach
                </select>
                @endif
                
                <select name="status" class="px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg dark:bg-dark-eval-2 text-sm">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">Filter</button>
                    <a href="{{ route('distributors.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 text-sm">Reset</a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl">
        @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
        @endif

        <div class="overflow-x-auto">
            <form id="bulkDeleteForm" method="POST" action="{{ route('distributors.bulk-delete') }}">
                @csrf
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            @if(auth()->user()->hasAnyRole(['Marketing', 'admin', 'PM']))
                            <th class="px-4 py-3 text-left">
                                <input type="checkbox" id="selectAll" class="rounded">
                            </th>
                            @endif
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Code</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Project</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">City</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($distributors as $dist)
                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            @if(auth()->user()->hasAnyRole(['Marketing', 'admin', 'PM']))
                            <td class="px-4 py-3">
                                <input type="checkbox" name="ids[]" value="{{ $dist->id }}" class="rounded item-checkbox">
                            </td>
                            @endif
                            <td class="px-4 py-3 font-mono text-sm">{{ $dist->code }}</td>
                            <td class="px-4 py-3 font-semibold">{{ $dist->name }}</td>
                            <td class="px-4 py-3 text-sm">{{ $dist->project->name }}</td>
                            <td class="px-4 py-3 text-sm">{{ $dist->city }}</td>
                            <td class="px-4 py-3 text-sm">{{ $dist->email }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $dist->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($dist->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('distributors.show', $dist) }}" class="text-blue-600 hover:text-blue-800">View</a>
                                    @can('manage-distributors')
                                    <a href="{{ route('distributors.edit', $dist) }}" class="text-yellow-600 hover:text-yellow-800">Edit</a>
                                    @endcan
                                    
                                    @if(auth()->user()->hasAnyRole(['admin', 'Marketing', 'PM']))
                                    <form method="POST" action="{{ route('distributors.destroy', $dist) }}" onsubmit="return confirm('Delete this distributor?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->hasAnyRole(['Marketing', 'admin', 'PM']) ? '8' : '7' }}" class="px-4 py-8 text-center text-gray-500">No distributors found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </form>
        </div>

        <div class="mt-4">
            {{ $distributors->appends(request()->query())->links() }}
        </div>
    </div>
    </div>

    @if(auth()->user()->hasAnyRole(['Marketing', 'admin', 'PM']))
    <script>
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const deleteBtn = document.getElementById('deleteBtn');

        selectAll?.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            toggleDeleteBtn();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', toggleDeleteBtn);
        });

        function toggleDeleteBtn() {
            const checked = document.querySelectorAll('.item-checkbox:checked').length;
            if (checked > 0) {
                deleteBtn.classList.remove('hidden');
            } else {
                deleteBtn.classList.add('hidden');
            }
        }

        function deleteSelected() {
            const checked = document.querySelectorAll('.item-checkbox:checked').length;
            if (checked === 0) {
                alert('Please select at least one item');
                return;
            }
            if (confirm(`Delete ${checked} selected distributor(s)?`)) {
                document.getElementById('bulkDeleteForm').submit();
            }
        }
    </script>
    @endif
</x-app-layout>

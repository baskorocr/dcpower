<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Retail Management</h2>
            <div class="flex gap-2">
                @php
                    $canDelete = false;
                    $user = auth()->user();
                    if ($user->hasAnyRole(['Marketing', 'admin', 'PM'])) {
                        $canDelete = true;
                    } elseif ($user->hasRole('distributor')) {
                        $canDelete = true;
                    }
                @endphp
                @if($canDelete)
                <button type="button" onclick="deleteSelected()" id="deleteBtn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 hidden">
                    Delete Selected
                </button>
                @endif
                @can('manage-retails')
                <a href="{{ route('retails.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Add Retail
                </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        <!-- Filters -->
        <div class="p-4 bg-white dark:bg-dark-eval-1 rounded-2xl">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, contact, city..." class="px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg dark:bg-dark-eval-2 text-sm">
                
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
                    <a href="{{ route('retails.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 text-sm">Reset</a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl">
        @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
        @endif

        <div class="overflow-x-auto">
            <form id="bulkDeleteForm" method="POST" action="{{ route('retails.bulk-delete') }}">
                @csrf
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            @if(auth()->user()->hasAnyRole(['Marketing', 'admin', 'PM']))
                            <th class="px-4 py-3 text-left">
                                <input type="checkbox" id="selectAll" class="rounded">
                            </th>
                            @endif
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Distributor</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Contact</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Location</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">PIN</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($retails as $retail)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            @if(auth()->user()->hasAnyRole(['Marketing', 'admin', 'PM']))
                            <td class="px-4 py-3">
                                <input type="checkbox" name="ids[]" value="{{ $retail->id }}" class="rounded item-checkbox">
                            </td>
                            @endif
                            <td class="px-4 py-3 font-medium">{{ $retail->name }}</td>
                            <td class="px-4 py-3 text-sm">{{ $retail->distributor->name }}</td>
                            <td class="px-4 py-3 text-sm">
                                <div>{{ $retail->contact_person }}</div>
                                <div class="text-gray-500">{{ $retail->phone }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $retail->city }}, {{ $retail->province }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-mono font-bold bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 rounded">{{ $retail->pin ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $retail->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($retail->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    @can('manage-retails')
                                    <a href="{{ route('retails.edit', $retail) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                                    @endcan
                                    
                                    @php
                                        $canDeleteRetail = false;
                                        $currentUser = auth()->user();
                                        
                                        if ($currentUser->hasAnyRole(['admin', 'Marketing', 'PM'])) {
                                            $canDeleteRetail = true;
                                        } elseif ($currentUser->hasRole('distributor')) {
                                            $userDistributor = \App\Models\Distributor::where('user_id', $currentUser->id)->first();
                                            if ($userDistributor && $retail->distributor_id == $userDistributor->id) {
                                                $canDeleteRetail = true;
                                            }
                                        }
                                    @endphp
                                    
                                    @if($canDeleteRetail)
                                    <form method="POST" action="{{ route('retails.destroy', $retail) }}" onsubmit="return confirm('Delete this retail?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->hasAnyRole(['Marketing', 'admin', 'PM']) ? '8' : '7' }}" class="px-4 py-8 text-center text-gray-500">No retails found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </form>
        </div>

        <div class="mt-4">
            {{ $retails->appends(request()->query())->links() }}
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
            if (confirm(`Delete ${checked} selected retail(s)?`)) {
                document.getElementById('bulkDeleteForm').submit();
            }
        }
    </script>
    @endif
</x-app-layout>

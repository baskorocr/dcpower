<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Distributors</h2>
            @can('manage-distributors')
            <a href="{{ route('distributors.create') }}" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-xl hover:scale-105 transition">
                Add Distributor
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="space-y-4">
        <!-- Filters -->
        <div class="p-4 bg-white dark:bg-dark-eval-1 rounded-2xl border-2 border-emerald-100 dark:border-emerald-800">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search code, name, city..." class="px-3 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg dark:bg-dark-eval-2 text-sm">
                
                @if($isAdmin)
                <select name="project_id" class="px-3 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg dark:bg-dark-eval-2 text-sm">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                    @endforeach
                </select>
                @endif
                
                <select name="status" class="px-3 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg dark:bg-dark-eval-2 text-sm">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm">Filter</button>
                    <a href="{{ route('distributors.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 text-sm">Reset</a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl border-2 border-emerald-100 dark:border-emerald-800">
        @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl">
            {{ session('success') }}
        </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b-2 border-emerald-100 dark:border-emerald-800">
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Code</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Name</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Project</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">City</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Phone</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Email</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($distributors as $distributor)
                    <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-emerald-50 dark:hover:bg-gray-800">
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $distributor->code }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $distributor->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $distributor->project->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $distributor->city }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $distributor->phone }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $distributor->email }}</td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 rounded-lg text-xs {{ $distributor->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($distributor->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div class="flex gap-2">
                                @can('manage-distributors')
                                <a href="{{ route('distributors.edit', $distributor) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                                @endcan
                                @can('manage-distributors')
                                <form action="{{ route('distributors.destroy', $distributor) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No distributors found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $distributors->appends(request()->query())->links() }}
        </div>
    </div>
    </div>
</x-app-layout>

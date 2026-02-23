<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Projects</h2>
            @can('manage-projects')
            <a href="{{ route('projects.create') }}" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-lg hover:scale-105 transition-transform">
                + New Project
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl border-2 border-emerald-100 dark:border-emerald-800">
        @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-50 dark:bg-emerald-900/20 border-l-4 border-emerald-500 text-emerald-700 dark:text-emerald-400">
            {{ session('success') }}
        </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-emerald-50 dark:bg-emerald-900/20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase">Warranty</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase">Created</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($projects as $project)
                    <tr class="hover:bg-emerald-50 dark:hover:bg-emerald-900/10">
                        <td class="px-4 py-3 font-medium">{{ $project->name }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full {{ $project->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($project->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $project->warranty_duration }} months</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $project->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('projects.show', $project) }}" class="text-emerald-600 hover:text-emerald-700">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">No projects found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $projects->links() }}
        </div>
    </div>
</x-app-layout>

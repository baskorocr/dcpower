<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $distributor->name }}</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $distributor->project->name }} • {{ $distributor->city }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('reports.distributor', ['distributor' => $distributor, 'export' => 1]) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export Excel
                </a>
                <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">Back</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl">
                <div class="text-sm text-gray-600 dark:text-gray-400">Total Received</div>
                <div class="text-3xl font-bold text-gray-800 dark:text-gray-100 mt-2">{{ $stats['total_received'] }}</div>
            </div>
            <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl">
                <div class="text-sm text-gray-600 dark:text-gray-400">In Stock</div>
                <div class="text-3xl font-bold text-blue-600 mt-2">{{ $stats['in_stock'] }}</div>
            </div>
            <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl">
                <div class="text-sm text-gray-600 dark:text-gray-400">At Retail</div>
                <div class="text-3xl font-bold text-purple-600 mt-2">{{ $stats['at_retail'] }}</div>
            </div>
            <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl">
                <div class="text-sm text-gray-600 dark:text-gray-400">Sold</div>
                <div class="text-3xl font-bold text-green-600 mt-2">{{ $stats['sold'] }}</div>
            </div>
        </div>

        <!-- Retails Table -->
        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl">
            <h3 class="text-lg font-semibold mb-4">Retail Breakdown</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Retail Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Contact</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Location</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400">Stock</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400">Sold</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($retails as $retail)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-3 font-medium">{{ $retail->name }}</td>
                            <td class="px-4 py-3 text-sm">
                                <div>{{ $retail->contact_person }}</div>
                                <div class="text-gray-500">{{ $retail->phone }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $retail->city }}, {{ $retail->province }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">{{ $retail->stock_count }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">{{ $retail->sold_count }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $retail->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($retail->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('reports.retail', $retail) }}" class="text-blue-600 hover:text-blue-800 font-medium">View Details</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">No retails found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Sales & Stock Reports</h2>
            <a href="{{ route('reports.index', array_merge(request()->all(), ['export' => 1])) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export Excel
            </a>
        </div>
    </x-slot>

    <div class="space-y-4">
        <!-- Filters -->
        <div class="p-4 bg-white dark:bg-dark-eval-1 rounded-2xl">
            <form method="GET" id="filterForm" class="grid grid-cols-1 md:grid-cols-6 gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search distributor..." class="px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg dark:bg-dark-eval-2 text-sm">
                
                <select name="project_id" id="project_id" onchange="document.getElementById('filterForm').submit()" class="px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg dark:bg-dark-eval-2 text-sm">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                    @endforeach
                </select>
                
                <select name="variant" class="px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg dark:bg-dark-eval-2 text-sm">
                    <option value="">All Variants</option>
                    @foreach($variants as $variant)
                        <option value="{{ $variant->variant }}" {{ request('variant') == $variant->variant ? 'selected' : '' }}>{{ $variant->variant }}</option>
                    @endforeach
                </select>
                
                <input type="date" name="start_date" value="{{ request('start_date') }}" placeholder="Start Date" class="px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg dark:bg-dark-eval-2 text-sm">
                
                <input type="date" name="end_date" value="{{ request('end_date') }}" placeholder="End Date" class="px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg dark:bg-dark-eval-2 text-sm">
                
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">Filter</button>
                    <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 text-sm">Reset</a>
                </div>
            </form>
        </div>

        <!-- Chart -->
        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl">
            <h3 class="text-lg font-semibold mb-4">Sales Trend</h3>
            <div style="height: 400px;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Distributors Table -->
        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl">
            <h3 class="text-lg font-semibold mb-4">Distributor Reports</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Code</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Project</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">City</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400">Stock</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400">Retails</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400">Sold</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($distributors as $dist)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-3 font-mono text-sm">{{ $dist->code }}</td>
                            <td class="px-4 py-3 font-medium">{{ $dist->name }}</td>
                            <td class="px-4 py-3 text-sm">{{ $dist->project->name }}</td>
                            <td class="px-4 py-3 text-sm">{{ $dist->city }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">{{ $dist->stock_count }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-semibold">{{ $dist->retail_count }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">{{ $dist->sold_count }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('reports.distributor', $dist) }}" class="text-blue-600 hover:text-blue-800 font-medium">View Details</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">No distributors found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartData['labels']),
                datasets: [{
                    label: 'Sales',
                    data: @json($chartData['data']),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>

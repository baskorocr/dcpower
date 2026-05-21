<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $retail->name }}</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $retail->distributor->name }} • {{ $retail->city }}, {{ $retail->province }}</p>
            </div>
            <a href="{{ route('reports.distributor', $retail->distributor) }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">Back</a>
        </div>
    </x-slot>

    <div class="space-y-4">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl">
                <div class="text-sm text-gray-600 dark:text-gray-400">Current Stock</div>
                <div class="text-3xl font-bold text-blue-600 mt-2">{{ $stats['in_stock'] }}</div>
            </div>
            <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl">
                <div class="text-sm text-gray-600 dark:text-gray-400">Total Sold</div>
                <div class="text-3xl font-bold text-green-600 mt-2">{{ $stats['sold'] }}</div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl">
            <h3 class="text-lg font-semibold mb-4">Product Details</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Serial Number</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Project</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Variant</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Manufactured</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($products as $product)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-3 font-mono text-sm">{{ $product->serial_number }}</td>
                            <td class="px-4 py-3 text-sm">{{ $product->project->name }}</td>
                            <td class="px-4 py-3 text-sm">{{ $product->variant ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($product->status === 'sold')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Sold</span>
                                @elseif($product->status === 'at_retail')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">In Stock</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $product->manufactured_at?->format('d M Y') ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">No products found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

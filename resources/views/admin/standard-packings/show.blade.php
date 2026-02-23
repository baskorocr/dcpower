<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Standard Packing Details</h2>
            <a href="{{ route('standard-packings.print', $standardPacking) }}" target="_blank" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg">
                Print Label
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Packing Info -->
        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl">
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Packing Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Packing Code</p>
                    <p class="text-2xl font-bold font-mono text-blue-600 dark:text-blue-400">{{ $standardPacking->packing_code }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Project</p>
                    <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $standardPacking->project->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Quantity</p>
                    <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $standardPacking->quantity }} units</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Created By</p>
                    <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $standardPacking->creator->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Packed At</p>
                    <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $standardPacking->packed_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Products in this Packing -->
        <div class="bg-white dark:bg-dark-eval-1 rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Products in this Packing</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Serial Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Manufactured At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($standardPacking->products as $index => $product)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-mono text-sm font-bold text-blue-600 dark:text-blue-400">{{ $product->serial_number }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full
                                    @if($product->status === 'manufactured') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @elseif($product->status === 'in_stock') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($product->status === 'sold') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @endif">
                                    {{ ucfirst($product->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $product->manufactured_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                    View Details
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                No products found in this packing
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

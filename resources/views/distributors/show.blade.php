<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Distributor Details</h2>
            <a href="{{ route('distributors.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                Back
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl">
            <h3 class="text-lg font-semibold mb-4">Basic Information</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-400">Code</label>
                    <p class="font-mono font-semibold">{{ $distributor->code }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-400">Status</label>
                    <p>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $distributor->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($distributor->status) }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-400">Name</label>
                    <p class="font-semibold">{{ $distributor->name }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-400">Project</label>
                    <p class="font-semibold">{{ $distributor->project->name }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-400">Email</label>
                    <p>{{ $distributor->email }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-400">Phone</label>
                    <p>{{ $distributor->phone ?? '-' }}</p>
                </div>
                <div class="col-span-2">
                    <label class="text-sm text-gray-600 dark:text-gray-400">Address</label>
                    <p>{{ $distributor->address ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-400">City</label>
                    <p>{{ $distributor->city ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-400">Province</label>
                    <p>{{ $distributor->province ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl">
            <h3 class="text-lg font-semibold mb-4">Stock Summary</h3>
            <div class="text-3xl font-bold text-blue-600">{{ number_format($stockCount) }} units</div>
        </div>
    </div>
</x-app-layout>

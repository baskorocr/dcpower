<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Warranty Claims</h2>
            @can('submit-claims')
            <a href="{{ route('warranty-claims.create') }}" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-lg hover:scale-105 transition-transform">
                + Submit Claim
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

        <!-- Filter -->
        <form method="GET" class="mb-6">
            <div class="flex gap-3">
                <select name="status" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-1 dark:text-gray-200">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Filter</button>
                <a href="{{ route('warranty-claims.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">Reset</a>
            </div>
        </form>

        <div class="space-y-4">
            @forelse($claims as $claim)
            <div class="p-4 border-2 border-emerald-100 dark:border-emerald-800 rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-900/10 transition-colors">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="font-bold text-gray-900 dark:text-gray-100">{{ $claim->claim_number }}</h3>
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $claim->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $claim->status === 'approved' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $claim->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                {{ $claim->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}">
                                {{ ucfirst($claim->status) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                            <span class="font-semibold">Product:</span> {{ $claim->product->standardPacking->variant ?? 'N/A' }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                            <span class="font-semibold">Type:</span> {{ ucfirst($claim->complaint_type) }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-semibold">Submitted:</span> {{ $claim->submitted_at->format('d M Y H:i') }}
                        </p>
                    </div>
                    <a href="{{ route('warranty-claims.show', $claim) }}" class="px-4 py-2 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-900/30">
                        View
                    </a>
                </div>
            </div>
            @empty
            <div class="py-8 text-center text-gray-500">No warranty claims found</div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $claims->links() }}
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">My Dashboard</h2>
    </x-slot>

    <!-- Quick Actions -->
    <div class="mb-6 p-6 bg-white dark:bg-dark-eval-1 rounded-2xl shadow-lg">
        <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-100">Quick Actions</h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('warranty-claims.create') }}" class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-lg hover:scale-105 transition-transform flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Submit Warranty Claim
            </a>
            <a href="{{ route('warranty-claims.index') }}" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-lg hover:scale-105 transition-transform flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                View My Claims
            </a>
            <a href="{{ route('sales.index') }}" class="px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:scale-105 transition-transform flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                View My Purchases
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Purchases -->
        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl shadow-lg">
            <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-100">Recent Purchases</h3>
            <div class="space-y-3">
                @forelse($recentPurchases as $purchase)
                <div class="p-4 bg-gray-50 dark:bg-dark-eval-2 rounded-lg">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $purchase->product->name }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $purchase->product->serial_number }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($purchase->warranty_end >= now()) bg-green-100 text-green-800 
                            @else bg-red-100 text-red-800 @endif">
                            @if($purchase->warranty_end >= now()) Active @else Expired @endif
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">{{ $purchase->sale_date->format('d M Y') }}</span>
                        <span class="text-gray-600 dark:text-gray-400">Warranty until: {{ $purchase->warranty_end->format('d M Y') }}</span>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 py-8">No purchases yet</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Claims -->
        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl shadow-lg">
            <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-100">Recent Claims</h3>
            <div class="space-y-3">
                @forelse($recentClaims as $claim)
                <div class="p-4 bg-gray-50 dark:bg-dark-eval-2 rounded-lg">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $claim->product->name }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $claim->claim_number }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($claim->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($claim->status === 'under_review') bg-blue-100 text-blue-800
                            @elseif($claim->status === 'approved') bg-green-100 text-green-800
                            @elseif($claim->status === 'rejected') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst(str_replace('_', ' ', $claim->status)) }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">{{ ucfirst($claim->complaint_type) }}</span>
                        <span class="text-gray-600 dark:text-gray-400">{{ $claim->submitted_at->format('d M Y') }}</span>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 py-8">No claims submitted yet</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>

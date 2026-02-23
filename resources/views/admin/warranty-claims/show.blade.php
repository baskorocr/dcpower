<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Claim: {{ $warrantyClaim->claim_number }}</h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Claim Info -->
        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl border-2 border-emerald-100 dark:border-emerald-800">
            @if(session('success'))
            <div class="mb-4 p-4 bg-emerald-50 dark:bg-emerald-900/20 border-l-4 border-emerald-500 text-emerald-700 dark:text-emerald-400">
                {{ session('success') }}
            </div>
            @endif

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                    <span class="px-3 py-1 text-sm rounded-full 
                        {{ $warrantyClaim->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $warrantyClaim->status === 'approved' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $warrantyClaim->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : '' }}
                        {{ $warrantyClaim->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}">
                        {{ ucfirst($warrantyClaim->status) }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Complaint Type</p>
                    <p class="font-semibold">{{ ucfirst($warrantyClaim->complaint_type) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Product</p>
                    <p class="font-semibold">{{ $warrantyClaim->product->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Invoice</p>
                    <p class="font-semibold">{{ $warrantyClaim->sale->invoice_no }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Submitted</p>
                    <p class="font-semibold">{{ $warrantyClaim->submitted_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Claimed By</p>
                    <p class="font-semibold">{{ $warrantyClaim->claimedBy->name }}</p>
                </div>
            </div>

            <div class="mb-4">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Complaint Description</p>
                <p class="p-3 bg-gray-50 dark:bg-dark-eval-2 rounded-lg">{{ $warrantyClaim->complaint_description }}</p>
            </div>

            @if($warrantyClaim->resolution_notes)
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Resolution Notes</p>
                <p class="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">{{ $warrantyClaim->resolution_notes }}</p>
            </div>
            @endif
        </div>

        <!-- Update Status (for managers) -->
        @can('manage-claims')
        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl border-2 border-emerald-100 dark:border-emerald-800">
            <h3 class="text-lg font-bold mb-4">Update Claim Status</h3>
            <form method="POST" action="{{ route('warranty-claims.update', $warrantyClaim) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select name="status" required class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2">
                        <option value="pending" {{ $warrantyClaim->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ $warrantyClaim->status === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $warrantyClaim->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="completed" {{ $warrantyClaim->status === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Resolution Notes</label>
                    <textarea name="resolution_notes" rows="3" class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2">{{ old('resolution_notes', $warrantyClaim->resolution_notes) }}</textarea>
                </div>

                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold rounded-lg hover:scale-105 transition-transform">
                    Update Claim
                </button>
            </form>
        </div>
        @endcan

        <!-- History -->
        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl border-2 border-emerald-100 dark:border-emerald-800">
            <h3 class="text-lg font-bold mb-4">Claim History</h3>
            <div class="space-y-3">
                @foreach($warrantyClaim->histories as $history)
                <div class="flex items-start gap-3 p-3 bg-emerald-50 dark:bg-emerald-900/10 rounded-lg">
                    <div class="w-2 h-2 mt-2 bg-emerald-500 rounded-full"></div>
                    <div class="flex-1">
                        <p class="font-semibold text-sm">
                            @if($history->old_status)
                            {{ ucfirst($history->old_status) }} → 
                            @endif
                            {{ ucfirst($history->new_status) }}
                        </p>
                        <p class="text-xs text-gray-500">{{ $history->actor->name }} • {{ $history->acted_at->format('d M Y H:i') }}</p>
                        @if($history->notes)
                        <p class="text-xs text-gray-600 mt-1">{{ $history->notes }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>

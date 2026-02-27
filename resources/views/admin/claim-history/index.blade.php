<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Claim History</h2>
    </x-slot>

    <div class="space-y-4">
        <!-- Filters -->
        <div class="p-4 bg-white dark:bg-dark-eval-1 rounded-2xl border-2 border-emerald-100 dark:border-emerald-800">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search claim/serial..." class="px-3 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg dark:bg-dark-eval-2 text-sm">
                
                <select name="status" class="px-3 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg dark:bg-dark-eval-2 text-sm">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
                
                <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From" class="px-3 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg dark:bg-dark-eval-2 text-sm">
                
                <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To" class="px-3 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg dark:bg-dark-eval-2 text-sm">
                
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm">Filter</button>
                    <a href="{{ route('claim-history.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 text-sm">Reset</a>
                </div>
            </form>
        </div>

        <!-- Claims Table -->
        <div class="bg-white dark:bg-dark-eval-1 rounded-2xl border-2 border-emerald-100 dark:border-emerald-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-emerald-50 dark:bg-emerald-900/20">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300">Claim Number</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300">Serial Number</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300">Product</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300">Submitted</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300">Claimed By</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($claims as $claim)
                        <tr class="hover:bg-gray-50 dark:hover:bg-dark-eval-2">
                            <td class="px-4 py-3 text-sm font-medium">{{ $claim->claim_number }}</td>
                            <td class="px-4 py-3 text-sm">{{ $claim->product->serial_number }}</td>
                            <td class="px-4 py-3 text-sm">{{ $claim->product->project->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="capitalize">{{ $claim->complaint_type }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($claim->status === 'pending')
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Pending</span>
                                @elseif($claim->status === 'approved')
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Approved</span>
                                @elseif($claim->status === 'rejected')
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Rejected</span>
                                @elseif($claim->status === 'completed')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Completed</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                {{ $claim->submitted_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $claim->claimedBy->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm">
                                <a href="{{ route('warranty-claims.show', $claim) }}" class="text-emerald-600 hover:text-emerald-800 font-medium">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">No claims found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $claims->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

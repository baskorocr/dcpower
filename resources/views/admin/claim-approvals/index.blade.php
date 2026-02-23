<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Warranty Claim Approvals</h2>
    </x-slot>

    <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl">
        @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-emerald-50 dark:bg-emerald-900">
                    <tr>
                        <th class="px-4 py-3 text-left">Claim #</th>
                        <th class="px-4 py-3 text-left">Product</th>
                        <th class="px-4 py-3 text-left">Customer</th>
                        <th class="px-4 py-3 text-left">Type</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Date</th>
                        <th class="px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($claims as $claim)
                    <tr class="border-b dark:border-gray-700">
                        <td class="px-4 py-3 font-mono text-sm">{{ $claim->claim_number }}</td>
                        <td class="px-4 py-3">{{ $claim->product->name }}</td>
                        <td class="px-4 py-3">{{ $claim->claimedBy->name }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                {{ ucfirst($claim->complaint_type) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($claim->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($claim->status === 'under_review') bg-blue-100 text-blue-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $claim->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">{{ $claim->submitted_at->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('claim-approvals.show', $claim) }}" class="text-emerald-600 hover:text-emerald-800">
                                Review
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">No pending claims</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $claims->links() }}
        </div>
    </div>
</x-app-layout>

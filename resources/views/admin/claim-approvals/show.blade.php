<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Review Claim: {{ $claim->claim_number }}</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl">
            <h3 class="text-lg font-bold mb-4">Claim Details</h3>
            
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-sm text-gray-500">Product</p>
                    <p class="font-semibold">{{ $claim->product->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Customer</p>
                    <p class="font-semibold">{{ $claim->claimedBy->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Invoice</p>
                    <p class="font-semibold">{{ $claim->sale->invoice_no }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Complaint Type</p>
                    <p class="font-semibold">{{ ucfirst($claim->complaint_type) }}</p>
                </div>
            </div>

            <div class="mb-6">
                <p class="text-sm text-gray-500 mb-2">Description</p>
                <p class="p-4 bg-gray-50 dark:bg-dark-eval-2 rounded-lg">{{ $claim->complaint_description }}</p>
            </div>

            @if($claim->photo_evidence)
            <div class="mb-6">
                <p class="text-sm text-gray-500 mb-2">Photo Evidence</p>
                <img src="{{ asset('storage/' . $claim->photo_evidence) }}" alt="Evidence" class="max-w-md rounded-lg border-2 border-emerald-200">
            </div>
            @endif
        </div>

        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl">
            <h3 class="text-lg font-bold mb-4">Approval Decision</h3>
            
            <form method="POST" action="{{ route('claim-approvals.approve', $claim) }}" class="mb-4">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">Resolution Notes *</label>
                    <textarea name="resolution_notes" rows="3" required class="w-full px-4 py-2 border-2 border-emerald-200 rounded-lg focus:ring-2 focus:ring-emerald-500" placeholder="Enter approval notes..."></textarea>
                </div>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Approve Claim
                </button>
            </form>

            <form method="POST" action="{{ route('claim-approvals.reject', $claim) }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">Rejection Reason *</label>
                    <textarea name="resolution_notes" rows="3" required class="w-full px-4 py-2 border-2 border-red-200 rounded-lg focus:ring-2 focus:ring-red-500" placeholder="Enter rejection reason..."></textarea>
                </div>
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Reject Claim
                </button>
            </form>
        </div>
    </div>
</x-app-layout>

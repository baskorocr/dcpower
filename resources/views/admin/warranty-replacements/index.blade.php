<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Warranty Replacements</h2>
    </x-slot>

    <div class="bg-white dark:bg-dark-eval-1 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-emerald-500 to-teal-500 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-bold">Claim Number</th>
                        <th class="px-6 py-4 text-left text-sm font-bold">Product Serial</th>
                        <th class="px-6 py-4 text-left text-sm font-bold">Customer</th>
                        <th class="px-6 py-4 text-left text-sm font-bold">Complaint</th>
                        <th class="px-6 py-4 text-left text-sm font-bold">Approved At</th>
                        <th class="px-6 py-4 text-left text-sm font-bold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($claims as $claim)
                    <tr class="hover:bg-emerald-50 dark:hover:bg-emerald-900/10">
                        <td class="px-6 py-4">
                            <span class="font-mono font-bold text-blue-600">{{ $claim->claim_number }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-mono text-sm">{{ $claim->product->serial_number }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-semibold">{{ $claim->customer_name }}</p>
                                <p class="text-xs text-gray-500">{{ $claim->whatsapp_number }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ Str::limit($claim->complaint_description, 50) }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            {{ $claim->approved_at?->format('d M Y H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('warranty-replacements.show', $claim) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold">
                                Process Replacement
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            No approved claims waiting for replacement
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($claims->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $claims->links() }}
        </div>
        @endif
    </div>
</x-app-layout>

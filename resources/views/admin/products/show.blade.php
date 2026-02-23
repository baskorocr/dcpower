<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $product->name }}</h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Product Info -->
        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl border-2 border-emerald-100 dark:border-emerald-800">
            <h3 class="text-lg font-bold mb-4">Product Information</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">QR Code</p>
                    <p class="font-mono font-semibold">{{ $product->qr_code }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Serial Number</p>
                    <p class="font-mono font-semibold">{{ $product->serial_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                    <span class="px-2 py-1 text-xs rounded-full 
                        @if($product->status === 'manufactured') bg-green-100 text-green-700
                        @elseif($product->status === 'in_distributor') bg-blue-100 text-blue-700
                        @elseif($product->status === 'sold') bg-purple-100 text-purple-700
                        @else bg-gray-100 text-gray-700
                        @endif">
                        {{ $product->status === 'in_distributor' ? 'In Distributor' : ucfirst($product->status) }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Manufactured</p>
                    <p class="font-semibold">{{ $product->manufactured_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Created By</p>
                    <p class="font-semibold">{{ $product->creator->name }}</p>
                </div>
            </div>
        </div>

        <!-- Trace Logs -->
        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl border-2 border-emerald-100 dark:border-emerald-800">
            <h3 class="text-lg font-bold mb-4">Trace History</h3>
            <div class="space-y-3">
                @forelse($product->traceLogs as $log)
                <div class="flex items-start gap-3 p-3 bg-emerald-50 dark:bg-emerald-900/10 rounded-lg">
                    <div class="w-2 h-2 mt-2 bg-emerald-500 rounded-full"></div>
                    <div class="flex-1">
                        <p class="font-semibold text-sm">{{ ucfirst(str_replace('_', ' ', $log->event_type ?? $log->action)) }}</p>
                        <p class="text-xs text-gray-500">{{ $log->user->name }} • {{ $log->scanned_at->format('d M Y H:i') }}</p>
                        @if($log->location)
                        <p class="text-xs text-gray-600 mt-1">📍 {{ $log->location }}</p>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-sm">No trace logs yet</p>
                @endforelse
            </div>
        </div>

        <!-- Sale Info -->
        @if($product->sale)
        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl border-2 border-emerald-100 dark:border-emerald-800">
            <h3 class="text-lg font-bold mb-4">Sale Information</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Invoice</p>
                    <p class="font-semibold">{{ $product->sale->invoice_no }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Buyer</p>
                    <p class="font-semibold">{{ $product->sale->buyer_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Sale Date</p>
                    <p class="font-semibold">{{ $product->sale->sale_date->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Warranty</p>
                    <p class="font-semibold">{{ $product->sale->warranty_start->format('d M Y') }} - {{ $product->sale->warranty_end->format('d M Y') }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Warranty Claims -->
        @if($product->warrantyClaims->count() > 0)
        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl border-2 border-emerald-100 dark:border-emerald-800">
            <h3 class="text-lg font-bold mb-4">Warranty Claims</h3>
            <div class="space-y-3">
                @foreach($product->warrantyClaims as $claim)
                <div class="p-3 border border-emerald-200 dark:border-emerald-700 rounded-lg">
                    <div class="flex items-center justify-between">
                        <p class="font-semibold">{{ $claim->claim_number }}</p>
                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">{{ ucfirst($claim->status) }}</span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $claim->complaint_description }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</x-app-layout>

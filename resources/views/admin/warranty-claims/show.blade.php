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
                    <p class="text-sm text-gray-500 dark:text-gray-400">Product Serial Number</p>
                    <p class="font-semibold">{{ $warrantyClaim->product->serial_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Product Name</p>
                    <p class="font-semibold">{{ $warrantyClaim->product->project->name ?? 'N/A' }}</p>
                </div>
                @php
                    $latestRetailMovement = $warrantyClaim->product->stockMovements
                        ->where('retail_id', '!=', null)
                        ->sortByDesc('moved_at')
                        ->first();
                    
                    // Fallback ke trace log jika tidak ada di stock movements
                    $retailName = null;
                    if ($latestRetailMovement && $latestRetailMovement->retail) {
                        $retailName = $latestRetailMovement->retail->name;
                    } else {
                        $retailTrace = $warrantyClaim->product->traceLogs
                            ->where('event_type', 'stock_out_retail')
                            ->sortByDesc('scanned_at')
                            ->first();
                        if ($retailTrace) {
                            $retailName = $retailTrace->location;
                        }
                    }
                @endphp
                @if($retailName)
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Retail</p>
                    <p class="font-semibold">{{ $retailName }}</p>
                </div>
                @endif
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Nomor WhatsApp</p>
                    <p class="font-semibold">{{ $warrantyClaim->whatsapp_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pembelian</p>
                    <p class="font-semibold">{{ ucfirst($warrantyClaim->purchase_type) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal Pembelian</p>
                    <p class="font-semibold">{{ $warrantyClaim->purchase_date?->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal Baterai Bermasalah</p>
                    <p class="font-semibold">{{ $warrantyClaim->battery_issue_date?->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tipe Motor</p>
                    <p class="font-semibold">{{ $warrantyClaim->motor_type }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pernah Modifikasi?</p>
                    <p class="font-semibold">{{ $warrantyClaim->has_modification ? 'Ya' : 'Tidak' }}</p>
                </div>
                @if($warrantyClaim->has_modification && $warrantyClaim->modification_types)
                <div class="col-span-2">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Jenis Modifikasi</p>
                    <div class="flex flex-wrap gap-2 mt-1">
                        @foreach($warrantyClaim->modification_types as $mod)
                        <span class="px-3 py-1 text-sm bg-orange-100 text-orange-700 rounded-full">
                            {{ ucfirst(str_replace('_', ' ', $mod)) }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif
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

            @if($warrantyClaim->photo_evidence)
            <div class="mb-4">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Photo Evidence</p>
                <img src="{{ asset('storage/' . $warrantyClaim->photo_evidence) }}" alt="Evidence" class="max-w-md rounded-lg border-2 border-gray-200 cursor-pointer hover:opacity-80 transition" onclick="openImageModal(this.src)">
            </div>
            @endif

            <!-- Image Modal -->
            <div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4" onclick="closeImageModal()">
                <div class="relative max-w-4xl max-h-full" onclick="event.stopPropagation()">
                    <button onclick="closeImageModal()" class="absolute -top-12 right-0 text-white bg-black bg-opacity-50 rounded-full w-10 h-10 flex items-center justify-center hover:bg-opacity-75 text-xl">
                        ✕
                    </button>
                    <img id="modalImage" src="" alt="Evidence" class="max-w-full max-h-screen rounded-lg">
                </div>
            </div>

            <script>
                function openImageModal(src) {
                    document.getElementById('modalImage').src = src;
                    document.getElementById('imageModal').classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
                
                function closeImageModal() {
                    document.getElementById('imageModal').classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }
            </script>

            @if($warrantyClaim->resolution_notes)
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Resolution Notes</p>
                <p class="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">{{ $warrantyClaim->resolution_notes }}</p>
            </div>
            @endif
        </div>

        <!-- Product Trace -->
        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl border-2 border-emerald-100 dark:border-emerald-800">
            <h3 class="text-lg font-bold mb-4">Product Trace</h3>
            <div class="space-y-3">
                <!-- Manufacture -->
                <div class="flex items-start gap-3 p-3 bg-blue-50 dark:bg-blue-900/10 rounded-lg">
                    <div class="w-2 h-2 mt-2 bg-blue-500 rounded-full"></div>
                    <div class="flex-1">
                        <p class="font-semibold text-sm">Manufacture</p>
                        <p class="text-xs text-gray-500">{{ $warrantyClaim->product->project->name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-400">{{ $warrantyClaim->product->manufactured_at?->format('d M Y H:i') ?? 'N/A' }}</p>
                    </div>
                </div>

                <!-- Distributor -->
                @php
                    $distributorMovement = $warrantyClaim->product->stockMovements
                        ->where('type', 'in')
                        ->where('distributor_id', '!=', null)
                        ->sortBy('moved_at')
                        ->first();
                @endphp
                @if($distributorMovement && $distributorMovement->distributor)
                <div class="flex items-start gap-3 p-3 bg-purple-50 dark:bg-purple-900/10 rounded-lg">
                    <div class="w-2 h-2 mt-2 bg-purple-500 rounded-full"></div>
                    <div class="flex-1">
                        <p class="font-semibold text-sm">Distributor</p>
                        <p class="text-xs text-gray-500">{{ $distributorMovement->distributor->name }}</p>
                        <p class="text-xs text-gray-400">{{ $distributorMovement->moved_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
                @endif

                <!-- Retail -->
                @php
                    $retailMovement = $warrantyClaim->product->stockMovements
                        ->where('retail_id', '!=', null)
                        ->sortByDesc('moved_at')
                        ->first();
                    
                    // Fallback ke trace log
                    $retailTrace = null;
                    if (!$retailMovement || !$retailMovement->retail) {
                        $retailTrace = $warrantyClaim->product->traceLogs
                            ->where('event_type', 'stock_out_retail')
                            ->sortByDesc('scanned_at')
                            ->first();
                    }
                @endphp
                @if(($retailMovement && $retailMovement->retail) || $retailTrace)
                <div class="flex items-start gap-3 p-3 bg-emerald-50 dark:bg-emerald-900/10 rounded-lg">
                    <div class="w-2 h-2 mt-2 bg-emerald-500 rounded-full"></div>
                    <div class="flex-1">
                        <p class="font-semibold text-sm">Retail</p>
                        <p class="text-xs text-gray-500">
                            {{ $retailMovement && $retailMovement->retail ? $retailMovement->retail->name : ($retailTrace ? $retailTrace->location : 'N/A') }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $retailMovement ? $retailMovement->moved_at->format('d M Y H:i') : ($retailTrace ? $retailTrace->scanned_at->format('d M Y H:i') : 'N/A') }}
                        </p>
                    </div>
                </div>
                @endif
            </div>
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

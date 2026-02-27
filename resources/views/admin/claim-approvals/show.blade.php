<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Review Claim: {{ $claim->claim_number }}</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl">
            <h3 class="text-lg font-bold mb-4">Claim Details</h3>
            
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-sm text-gray-500">Product Name</p>
                    <p class="font-semibold">{{ $claim->product->project->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Serial Number</p>
                    <p class="font-semibold">{{ $claim->product->serial_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Customer</p>
                    <p class="font-semibold">{{ $claim->claimedBy->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Nomor WhatsApp</p>
                    <p class="font-semibold">{{ $claim->whatsapp_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Complaint Type</p>
                    <p class="font-semibold">{{ ucfirst($claim->complaint_type) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Submitted</p>
                    <p class="font-semibold">{{ $claim->submitted_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Pembelian</p>
                    <p class="font-semibold">{{ ucfirst($claim->purchase_type) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tanggal Pembelian</p>
                    <p class="font-semibold">{{ $claim->purchase_date?->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tanggal Baterai Bermasalah</p>
                    <p class="font-semibold">{{ $claim->battery_issue_date?->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tipe Motor</p>
                    <p class="font-semibold">{{ $claim->motor_type }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Pernah Modifikasi?</p>
                    <p class="font-semibold">{{ $claim->has_modification ? 'Ya' : 'Tidak' }}</p>
                </div>
                @if($claim->has_modification && $claim->modification_types)
                <div class="col-span-2">
                    <p class="text-sm text-gray-500 mb-1">Jenis Modifikasi</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($claim->modification_types as $mod)
                        <span class="px-3 py-1 text-sm bg-orange-100 text-orange-700 rounded-full">
                            {{ ucfirst(str_replace('_', ' ', $mod)) }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div class="mb-6">
                <p class="text-sm text-gray-500 mb-2">Description</p>
                <p class="p-4 bg-gray-50 dark:bg-dark-eval-2 rounded-lg">{{ $claim->complaint_description }}</p>
            </div>

            @if($claim->photo_evidence)
            <div class="mb-6">
                <p class="text-sm text-gray-500 mb-2">Photo Evidence</p>
                <img src="{{ asset('storage/' . $claim->photo_evidence) }}" alt="Evidence" class="max-w-md rounded-lg border-2 border-emerald-200 cursor-pointer hover:opacity-80 transition" onclick="openImageModal(this.src)">
            </div>
            @endif
        </div>

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

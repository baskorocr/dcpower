<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Process Replacement</h2>
            <a href="{{ route('warranty-replacements.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg">
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Claim Details -->
        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl">
            <h3 class="text-lg font-bold mb-4">Claim Information</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Claim Number</p>
                    <p class="font-mono font-bold text-blue-600">{{ $warrantyClaim->claim_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">Approved</span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Customer Name</p>
                    <p class="font-semibold">{{ $warrantyClaim->customer_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">WhatsApp</p>
                    <p class="font-semibold">{{ $warrantyClaim->whatsapp_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Defective Product Serial</p>
                    <p class="font-mono font-semibold">{{ $warrantyClaim->product->serial_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Approved By</p>
                    <p class="font-semibold">{{ $warrantyClaim->approver?->name ?? '-' }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-sm text-gray-500">Complaint Description</p>
                    <p class="font-semibold">{{ $warrantyClaim->complaint_description }}</p>
                </div>
            </div>
        </div>

        <!-- Scanner Section -->
        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl">
            <h3 class="text-lg font-bold mb-4">Scan Replacement Product</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Scan the QR code of the replacement product. The product must be in your stock with status "In Distributor".
            </p>

            <div class="max-w-md mx-auto">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Serial Number</label>
                    <input type="text" id="serialNumber" 
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-gray-800" 
                        placeholder="Scan or enter serial number" autofocus>
                </div>

                <button onclick="processReplacement()" 
                    class="w-full px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold rounded-lg hover:scale-105 transition-transform">
                    Process Replacement
                </button>

                <div id="message" class="mt-4 hidden"></div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('serialNumber').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                processReplacement();
            }
        });

        function processReplacement() {
            const serialNumber = document.getElementById('serialNumber').value.trim();
            const messageDiv = document.getElementById('message');

            if (!serialNumber) {
                showMessage('Please enter or scan a serial number', 'error');
                return;
            }

            fetch('{{ route("warranty-replacements.scan", $warrantyClaim) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ serial_number: serialNumber })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = '{{ route("warranty-replacements.index") }}';
                    }, 1500);
                } else {
                    showMessage(data.message, 'error');
                    document.getElementById('serialNumber').value = '';
                    document.getElementById('serialNumber').focus();
                }
            })
            .catch(error => {
                showMessage('An error occurred', 'error');
            });
        }

        function showMessage(text, type) {
            const messageDiv = document.getElementById('message');
            messageDiv.className = `mt-4 p-4 rounded-lg ${type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`;
            messageDiv.textContent = text;
            messageDiv.classList.remove('hidden');
        }
    </script>
</x-app-layout>

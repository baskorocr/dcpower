<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Scan Serial Number</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
            <p class="text-green-700 dark:text-green-400">✓ {{ session('success') }}</p>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Scan Form -->
            <div class="lg:col-span-2">
                <div class="p-8 bg-white dark:bg-dark-eval-1 rounded-2xl border-2 border-blue-100 dark:border-blue-800">
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-100 dark:bg-blue-900 rounded-full mb-4">
                            <svg class="w-10 h-10 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-2">Scan Serial Number</h3>
                        @if($project && $project->standard_packing_quantity)
                        <p class="text-gray-600 dark:text-gray-400">Standard Packing: <span class="font-bold text-blue-600">{{ $project->standard_packing_quantity }} units</span></p>
                        @else
                        <p class="text-gray-600 dark:text-gray-400">No standard packing configured</p>
                        @endif
                    </div>

                    <div class="mb-6">
                        <input 
                            type="text" 
                            id="serial_input"
                            autofocus
                            autocomplete="off"
                            placeholder="Ready to scan..."
                            class="w-full px-6 py-4 text-lg text-center border-2 border-blue-300 dark:border-blue-700 rounded-lg focus:ring-4 focus:ring-blue-500 dark:bg-dark-eval-2 font-mono"
                        >
                    </div>

                    @if($project && $project->standard_packing_quantity)
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Progress</span>
                            <span class="text-sm font-bold text-blue-600 dark:text-blue-400" id="progress_text">0 / {{ $project->standard_packing_quantity }}</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                            <div id="progress_bar" class="bg-blue-600 h-3 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                    </div>
                    @endif

                    @if($project && $project->use_variants && $project->variants)
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Variant *</label>
                        <select id="variant_select" required class="w-full px-4 py-3 border-2 border-blue-300 dark:border-blue-700 rounded-lg focus:ring-4 focus:ring-blue-500 dark:bg-dark-eval-2 text-base">
                            <option value="">-- Select Variant --</option>
                            @foreach($project->variants as $variant)
                            <option value="{{ $variant }}">{{ $variant }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <button 
                        id="submit_btn" 
                        onclick="submitProducts()"
                        class="w-full px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        @if($project && $project->standard_packing_quantity) disabled @endif
                    >
                        <span id="btn_text">
                            @if($project && $project->standard_packing_quantity)
                            Scan {{ $project->standard_packing_quantity }} items to continue
                            @else
                            Submit Products
                            @endif
                        </span>
                    </button>
                </div>

                <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <p class="text-sm text-blue-700 dark:text-blue-400">
                        <strong>Instructions:</strong> Scan serial numbers with your Zebra scanner. 
                        @if($project && $project->standard_packing_quantity)
                        After scanning {{ $project->standard_packing_quantity }} items, a standard packing code will be generated automatically.
                        @endif
                    </p>
                </div>
            </div>

            <!-- Scanned Items List -->
            <div class="lg:col-span-1">
                <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl border-2 border-gray-100 dark:border-gray-800">
                    <h4 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Scanned Items</h4>
                    <div id="scanned_list" class="space-y-2 max-h-96 overflow-y-auto">
                        <p class="text-gray-500 dark:text-gray-400 text-sm text-center py-8">No items scanned yet</p>
                    </div>
                    <button 
                        onclick="clearAll()" 
                        id="clear_btn"
                        class="mt-4 w-full px-4 py-2 bg-red-100 hover:bg-red-200 dark:bg-red-900/20 dark:hover:bg-red-900/40 text-red-600 dark:text-red-400 font-medium rounded-lg transition-colors hidden"
                    >
                        Clear All
                    </button>
                </div>
            </div>
        </div>

        <!-- Print Modal -->
        <div id="print_modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-dark-eval-1 rounded-2xl p-8 max-w-md w-full mx-4">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 dark:bg-green-900 rounded-full mb-4">
                        <svg class="w-10 h-10 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-2">Standard Packing Created!</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Packing Code:</p>
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg mb-6">
                        <p id="packing_code" class="text-3xl font-bold text-blue-600 dark:text-blue-400 font-mono"></p>
                    </div>
                    <div class="flex gap-3">
                        <button onclick="printLabel()" class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition-colors">
                            Print Label
                        </button>
                        <button onclick="closeModal()" class="flex-1 px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 font-bold rounded-lg transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const qrInput = document.getElementById('serial_input');
        const variantSelect = document.getElementById('variant_select');
        const scannedList = document.getElementById('scanned_list');
        const clearBtn = document.getElementById('clear_btn');
        const submitBtn = document.getElementById('submit_btn');
        const btnText = document.getElementById('btn_text');
        const printModal = document.getElementById('print_modal');
        const packingCodeEl = document.getElementById('packing_code');
        
        const standardPackingQty = {{ $project && $project->standard_packing_quantity ? $project->standard_packing_quantity : 'null' }};
        const useVariants = {{ $project && $project->use_variants ? 'true' : 'false' }};
        let scannedSerials = [];
        let currentPackingId = null;

        console.log('Script loaded, qrInput:', qrInput);

        // Force focus
        setTimeout(() => qrInput.focus(), 100);
        setInterval(() => {
            const activeElement = document.activeElement;
            // Don't force focus if user is interacting with variant select
            if (variantSelect && activeElement === variantSelect) {
                return;
            }
            if (activeElement !== qrInput && printModal.classList.contains('hidden')) {
                qrInput.focus();
            }
        }, 100);

        // Handle scan - detect fast input from scanner
        let scanTimeout;
        qrInput.addEventListener('input', function(e) {
            clearTimeout(scanTimeout);
            scanTimeout = setTimeout(() => {
                const serial = this.value.trim();
                console.log('Input timeout, processing:', serial);
                
                if (!serial) return;
                
                if (scannedSerials.includes(serial)) {
                    alert('Serial number already scanned!');
                    this.value = '';
                    return;
                }
                
                addSerial(serial);
                this.value = '';
                
                // Auto submit if reached standard packing quantity
                if (standardPackingQty && scannedSerials.length === standardPackingQty) {
                    setTimeout(() => submitProducts(), 500);
                }
            }, 200); // Wait 200ms after last input
        });

        // Also handle Enter key for manual input
        qrInput.addEventListener('keypress', function(e) {
            console.log('Key pressed:', e.key, 'Value:', this.value);
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(scanTimeout); // Cancel timeout
                const serial = this.value.trim();
                console.log('Processing serial:', serial);
                
                if (!serial) return;
                
                if (scannedSerials.includes(serial)) {
                    alert('Serial number already scanned!');
                    this.value = '';
                    return;
                }
                
                addSerial(serial);
                this.value = '';
                
                // Auto submit if reached standard packing quantity
                if (standardPackingQty && scannedSerials.length === standardPackingQty) {
                    setTimeout(() => submitProducts(), 500);
                }
            }
        });

        function addSerial(serial) {
            console.log('addSerial called with:', serial);
            scannedSerials.push(serial);
            console.log('scannedSerials now:', scannedSerials);
            updateList();
            updateProgress();
        }

        function updateList() {
            console.log('updateList called, length:', scannedSerials.length);
            if (scannedSerials.length === 0) {
                scannedList.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-sm text-center py-8">No items scanned yet</p>';
                clearBtn.classList.add('hidden');
            } else {
                scannedList.innerHTML = scannedSerials.map((serial, index) => `
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <span class="text-sm font-mono text-gray-700 dark:text-gray-300">${serial}</span>
                        <button onclick="removeSerial(${index})" class="text-red-500 hover:text-red-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                `).join('');
                clearBtn.classList.remove('hidden');
            }
            console.log('updateList done, innerHTML:', scannedList.innerHTML.substring(0, 100));
        }

        function updateProgress() {
            if (standardPackingQty) {
                const progress = (scannedSerials.length / standardPackingQty) * 100;
                document.getElementById('progress_bar').style.width = progress + '%';
                document.getElementById('progress_text').textContent = `${scannedSerials.length} / ${standardPackingQty}`;
                
                if (scannedSerials.length >= standardPackingQty) {
                    submitBtn.disabled = false;
                    btnText.textContent = 'Create Standard Packing';
                } else {
                    submitBtn.disabled = true;
                    btnText.textContent = `Scan ${standardPackingQty - scannedSerials.length} more items`;
                }
            } else {
                submitBtn.disabled = scannedSerials.length === 0;
            }
        }

        function removeSerial(index) {
            scannedSerials.splice(index, 1);
            updateList();
            updateProgress();
        }

        function clearAll() {
            if (confirm('Clear all scanned items?')) {
                scannedSerials = [];
                updateList();
                updateProgress();
            }
        }

        async function submitProducts() {
            if (scannedSerials.length === 0) return;
            
            // Check variant if required
            if (useVariants && variantSelect) {
                const variant = variantSelect.value;
                if (!variant) {
                    alert('Please select a variant first!');
                    return;
                }
            }
            
            submitBtn.disabled = true;
            btnText.textContent = 'Creating products...';

            try {
                const payload = {
                    serial_numbers: scannedSerials
                };
                
                if (useVariants && variantSelect) {
                    payload.variant = variantSelect.value;
                }

                const response = await fetch('{{ route("products.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (data.success) {
                    if (data.standard_packing) {
                        // Show print modal
                        packingCodeEl.textContent = data.standard_packing.code;
                        currentPackingId = data.standard_packing.id;
                        printModal.classList.remove('hidden');
                        
                        // Auto print after 1 second
                        setTimeout(() => printLabel(), 1000);
                    } else {
                        alert(data.message);
                        scannedSerials = [];
                        updateList();
                        updateProgress();
                    }
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                alert('Error creating products: ' + error.message);
            } finally {
                submitBtn.disabled = false;
                btnText.textContent = standardPackingQty ? `Scan ${standardPackingQty} items to continue` : 'Submit Products';
            }
        }

        function printLabel() {
            // Open print page in new window using packing ID
            if (currentPackingId) {
                window.open(`/standard-packings/${currentPackingId}/print`, '_blank');
            }
        }

        function closeModal() {
            printModal.classList.add('hidden');
            scannedSerials = [];
            updateList();
            updateProgress();
            qrInput.focus();
        }
    </script>
</x-app-layout>

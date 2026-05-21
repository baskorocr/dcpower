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

        <!-- Toast Notification -->
        <div id="toast" class="hidden fixed top-4 right-4 bg-green-600 text-white px-6 py-4 rounded-lg shadow-lg z-50 max-w-md">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <div>
                    <p class="font-bold mb-1">Products Created Successfully!</p>
                    <p id="toast_message" class="text-sm opacity-90"></p>
                </div>
            </div>
        </div>

        <!-- Duplicate Modal -->
        <div id="duplicate_modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-dark-eval-1 rounded-2xl p-8 max-w-md w-full mx-4">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-red-100 dark:bg-red-900 rounded-full mb-4">
                        <svg class="w-10 h-10 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-2">Duplicate Serial Number!</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Serial number already exists:</p>
                    <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg mb-6">
                        <p id="duplicate_serial" class="text-xl font-bold text-red-600 dark:text-red-400 font-mono"></p>
                    </div>
                    <button onclick="closeDuplicateModal()" class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition-colors">
                        OK
                    </button>
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
        
        const standardPackingQty = {{ $project && $project->standard_packing_quantity ? $project->standard_packing_quantity : 'null' }};
        const useVariants = {{ $project && $project->use_variants ? 'true' : 'false' }};
        let scannedSerials = [];

        console.log('Script loaded, qrInput:', qrInput);

        // Initial focus only
        setTimeout(() => qrInput.focus(), 100);
        
        // Re-focus only after successful scan
        function refocusInput() {
            setTimeout(() => qrInput.focus(), 100);
        }

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
                refocusInput();
                
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
                refocusInput();
                
                // Auto submit if reached standard packing quantity
                if (standardPackingQty && scannedSerials.length === standardPackingQty) {
                    setTimeout(() => submitProducts(), 500);
                }
            }
        });

        async function addSerial(serial) {
            console.log('addSerial called with:', serial);
            
            // Prevent adding more than standard packing quantity
            if (standardPackingQty && scannedSerials.length >= standardPackingQty) {
                alert(`Cannot scan more than ${standardPackingQty} items for standard packing!`);
                return;
            }
            
            // Check if serial already exists in database
            try {
                const response = await fetch(`/api/check-serial/${serial}`);
                const data = await response.json();
                
                if (data.exists) {
                    // Show duplicate modal
                    document.getElementById('duplicate_serial').textContent = serial;
                    document.getElementById('duplicate_modal').classList.remove('hidden');
                    return;
                }
            } catch (error) {
                console.error('Error checking serial:', error);
            }
            
            scannedSerials.push(serial);
            console.log('scannedSerials now:', scannedSerials);
            updateList();
            updateProgress();
        }

        function closeDuplicateModal() {
            document.getElementById('duplicate_modal').classList.add('hidden');
            qrInput.value = '';
            qrInput.focus();
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
                    // Show toast notification
                    const serialsList = data.products.map(p => p.serial_number).join(', ');
                    const toastMessage = data.standard_packing 
                        ? `Packing: ${data.standard_packing.code}<br>Serials: ${serialsList}`
                        : `Serials: ${serialsList}`;
                    
                    document.getElementById('toast_message').innerHTML = toastMessage;
                    document.getElementById('toast').classList.remove('hidden');
                    
                    // Hide toast after 5 seconds
                    setTimeout(() => {
                        document.getElementById('toast').classList.add('hidden');
                    }, 5000);
                    
                    // Reset form
                    scannedSerials = [];
                    updateList();
                    updateProgress();
                    qrInput.focus();
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
    </script>
</x-app-layout>

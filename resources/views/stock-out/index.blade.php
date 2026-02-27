<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
            @if($isDistributor)
                Stock Out to Retail - Multiple Scan
            @else
                Stock Out to Distributor - Multiple Scan
            @endif
        </h2>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
        <div class="p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="p-4 bg-red-100 text-red-800 rounded-lg">{{ session('error') }}</div>
        @endif

        @if($isDistributor)
        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 rounded-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h3 class="font-semibold text-blue-800 dark:text-blue-300">Distributor Mode</h3>
                    <p class="text-sm text-blue-700 dark:text-blue-400 mt-1">
                        You are scanning products from your distributor stock to send to retail. Only products currently at your location can be scanned.
                    </p>
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Scan Section -->
            <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl">
                <h3 class="text-lg font-semibold mb-4">Scan Products</h3>
                
                @unless(auth()->user()->hasAnyRole(['admin', 'project_manager', 'qa']))
                <!-- Camera Scanner -->
                <div class="mb-4">
                    <video id="video" class="w-full rounded-lg border-2 border-gray-300 dark:border-gray-600" style="max-height: 400px; object-fit: cover;"></video>
                    <canvas id="canvas" hidden></canvas>
                </div>
                @endunless

                <div class="mb-4">
                    <input type="text" id="qr_code" placeholder="{{ auth()->user()->hasAnyRole(['admin', 'project_manager', 'qa']) ? 'Enter serial number manually' : 'Or enter serial number manually' }}" 
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700">
                </div>

                <div id="scan-message" class="hidden mb-4 p-3 rounded-lg"></div>

                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Scanned: <span id="scan-count" class="font-bold text-blue-600">0</span> items
                </div>
            </div>

            <!-- Scanned Items -->
            <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl">
                <h3 class="text-lg font-semibold mb-4">Scanned Items</h3>
                <div id="scanned-items" class="space-y-2 max-h-96 overflow-y-auto">
                    <p class="text-gray-500 text-sm">No items scanned yet</p>
                </div>
            </div>
        </div>

        <!-- Process Form -->
        <form method="POST" action="{{ route('stock-out.process') }}" id="process-form" class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl">
            @csrf
            <h3 class="text-lg font-semibold mb-4">Process Stock Out</h3>
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">
                    @if($isDistributor)
                        Retail Destination *
                    @else
                        Destination Distributor *
                    @endif
                </label>
                @if($isDistributor)
                    <select name="retail_id" required class="w-full px-4 py-2 border rounded-lg dark:bg-gray-800 dark:border-gray-700 focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Retail Store</option>
                        @foreach($retailers as $retail)
                        <option value="{{ $retail->id }}">{{ $retail->name }} - {{ $retail->location }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="distributor_id" value="{{ $distributors->first()->id ?? '' }}">
                @else
                    <select name="distributor_id" required class="w-full px-4 py-2 border rounded-lg dark:bg-gray-800 dark:border-gray-700">
                        <option value="">Select Distributor</option>
                        @foreach($distributors as $dist)
                        <option value="{{ $dist->id }}">{{ $dist->name }} - {{ $dist->project->name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>

            <input type="hidden" name="products" id="products-input">

            <button type="submit" id="process-btn" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                @if($isDistributor)
                    Process Stock Out to Retail
                @else
                    Process Stock Out to Distributor
                @endif
            </button>
        </form>
    </div>

    <script src="https://unpkg.com/@zxing/library@latest"></script>
    <script>
        let scannedProducts = [];
        const qrInput = document.getElementById('qr_code');
        const scanMessage = document.getElementById('scan-message');
        const scannedItemsDiv = document.getElementById('scanned-items');
        const scanCount = document.getElementById('scan-count');
        const processBtn = document.getElementById('process-btn');
        const productsInput = document.getElementById('products-input');
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');

        @unless(auth()->user()->hasAnyRole(['admin', 'project_manager', 'qa']))
        // Initialize ZXing scanner
        const codeReader = new ZXing.BrowserMultiFormatReader();
        let selectedDeviceId;

        const constraints = {
            video: { 
                facingMode: "environment",
                width: { ideal: 1920 },
                height: { ideal: 1080 }
            }
        };

        codeReader.decodeFromConstraints(constraints, 'video', (result, err) => {
            if (result) {
                processQRCode(result.text);
            }
            if (err && !(err instanceof ZXing.NotFoundException)) {
                console.error('Scan error:', err);
            }
        }).catch((err) => {
            console.error('Camera error:', err);
            showMessage('Camera not available. Please use manual input.', 'error');
        });
        @endunless

        async function processQRCode(qrCode) {
            if (!qrCode) return;

            // Check if already scanned
            const alreadyScanned = scannedProducts.find(p => p.serial_number === qrCode || p.qr_code === qrCode);
            if (alreadyScanned) {
                showMessage('Product already scanned!', 'error');
                return;
            }

            try {
                const response = await fetch('{{ route("stock-out.scan") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ qr_code: qrCode })
                });

                const data = await response.json();

                if (data.success) {
                    if (data.is_packing) {
                        data.products.forEach(product => {
                            const alreadyExists = scannedProducts.find(p => p.id === product.id);
                            if (!alreadyExists) {
                                scannedProducts.push(product);
                            }
                        });
                        updateUI();
                        showMessage(`Packing ${data.packing_code} added: ${data.products.length} products`, 'success');
                    } else {
                        scannedProducts.push(data.product);
                        updateUI();
                        showMessage('Product added: ' + data.product.name, 'success');
                    }
                } else {
                    showMessage(data.message || 'Product not found', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('Error: ' + error.message, 'error');
            }
        }

        qrInput.addEventListener('keypress', async function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const qrCode = this.value.trim();
                if (qrCode) {
                    await processQRCode(qrCode);
                    this.value = '';
                }
            }
        });

        function updateUI() {
            scanCount.textContent = scannedProducts.length;
            
            if (scannedProducts.length === 0) {
                scannedItemsDiv.innerHTML = '<p class="text-gray-500 text-sm">No items scanned yet</p>';
                productsInput.value = '';
            } else {
                const grouped = scannedProducts.reduce((acc, p) => {
                    acc[p.id] = acc[p.id] || { ...p, count: 0 };
                    acc[p.id].count++;
                    return acc;
                }, {});

                scannedItemsDiv.innerHTML = Object.values(grouped).map((item, idx) => `
                    <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div>
                            <div class="font-semibold">${item.name}</div>
                            <div class="text-sm text-gray-600">${item.sku} - Qty: ${item.count}</div>
                        </div>
                       
                    </div>
                `).join('');

                productsInput.value = JSON.stringify(scannedProducts);
            }
        }

        function removeItem(index) {
            const grouped = Object.values(scannedProducts.reduce((acc, p) => {
                acc[p.id] = acc[p.id] || { ...p, count: 0 };
                acc[p.id].count++;
                return acc;
            }, {}));
            
            const itemToRemove = grouped[index];
            scannedProducts = scannedProducts.filter(p => p.id !== itemToRemove.id);
            updateUI();
            qrInput.focus();
        }

        function showMessage(message, type) {
            scanMessage.className = `mb-4 p-3 rounded-lg ${type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
            scanMessage.textContent = message;
            scanMessage.classList.remove('hidden');
            setTimeout(() => scanMessage.classList.add('hidden'), 3000);
        }
    </script>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Stock Out - Multiple Scan</h2>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
        <div class="p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="p-4 bg-red-100 text-red-800 rounded-lg">{{ session('error') }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Scan Section -->
            <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl">
                <h3 class="text-lg font-semibold mb-4">Scan Products</h3>
                
                <div class="mb-4">
                    <input type="text" id="qr_code" placeholder="Scan or enter serial number" 
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700"
                        autofocus>
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
                <label class="block text-sm font-medium mb-2">Distributor *</label>
                <select name="distributor_id" required class="w-full px-4 py-2 border rounded-lg dark:bg-gray-800 dark:border-gray-700">
                    <option value="">Select Distributor</option>
                    @foreach($distributors as $dist)
                    <option value="{{ $dist->id }}">{{ $dist->name }} - {{ $dist->project->name }}</option>
                    @endforeach
                </select>
            </div>

            <input type="hidden" name="products" id="products-input">

            <button type="submit" id="process-btn" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                Process Stock Out
            </button>
        </form>
    </div>

    <script>
        let scannedProducts = [];
        const qrInput = document.getElementById('qr_code');
        const scanMessage = document.getElementById('scan-message');
        const scannedItemsDiv = document.getElementById('scanned-items');
        const scanCount = document.getElementById('scan-count');
        const processBtn = document.getElementById('process-btn');
        const productsInput = document.getElementById('products-input');

        console.log('Stock-out script loaded');

        qrInput.addEventListener('keypress', async function(e) {
            console.log('Key pressed:', e.key);
            if (e.key === 'Enter') {
                e.preventDefault();
                const qrCode = this.value.trim();
                console.log('QR Code:', qrCode);
                if (!qrCode) return;

                // Check if already scanned
                const alreadyScanned = scannedProducts.find(p => p.serial_number === qrCode || p.qr_code === qrCode);
                if (alreadyScanned) {
                    showMessage('Product already scanned!', 'error');
                    this.value = '';
                    this.focus();
                    return;
                }

                try {
                    console.log('Fetching...');
                    const response = await fetch('{{ route("stock-out.scan") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ qr_code: qrCode })
                    });

                    const data = await response.json();
                    console.log('Response:', data);

                    if (data.success) {
                        if (data.is_packing) {
                            // Add all products from packing
                            data.products.forEach(product => {
                                const alreadyExists = scannedProducts.find(p => p.id === product.id);
                                if (!alreadyExists) {
                                    scannedProducts.push(product);
                                }
                            });
                            updateUI();
                            showMessage(`Packing ${data.packing_code} added: ${data.products.length} products`, 'success');
                        } else {
                            // Add single product
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

                this.value = '';
                this.focus();
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

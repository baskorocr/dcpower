<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Stock Out - Distributor ke Retail') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Scanner Section -->
                    <div class="mb-6">
                        <label class="block mb-2 text-sm font-medium text-gray-700">Scan Barcode Produk</label>
                        <div class="flex gap-2">
                            <input type="text" id="serialNumberInput" 
                                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Scan atau ketik serial number..." autofocus>
                            <button onclick="scanProduct()" 
                                class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                Scan
                            </button>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div id="productInfo" class="hidden p-4 mb-6 border rounded-lg bg-gray-50">
                        <h3 class="mb-3 text-lg font-semibold">Informasi Produk</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Serial Number</p>
                                <p id="productSerial" class="font-medium"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Project</p>
                                <p id="productProject" class="font-medium"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Stok di Distributor</p>
                                <p id="productStock" class="font-medium"></p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Jumlah Checkout</label>
                            <input type="number" id="checkoutQuantity" min="1" value="1"
                                class="w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <button onclick="checkout()" 
                            class="px-6 py-2 mt-4 text-white bg-green-600 rounded-md hover:bg-green-700">
                            Checkout ke Retail
                        </button>
                    </div>

                    <!-- Alert Messages -->
                    <div id="alertSuccess" class="hidden p-4 mb-4 text-green-700 bg-green-100 border border-green-400 rounded">
                        <p id="successMessage"></p>
                    </div>
                    <div id="alertError" class="hidden p-4 mb-4 text-red-700 bg-red-100 border border-red-400 rounded">
                        <p id="errorMessage"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentProduct = null;

        document.getElementById('serialNumberInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                scanProduct();
            }
        });

        function scanProduct() {
            const serialNumber = document.getElementById('serialNumberInput').value.trim();
            if (!serialNumber) {
                showError('Masukkan serial number');
                return;
            }

            fetch('{{ route("distributor.stock-out.scan") }}', {
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
                    currentProduct = data.product;
                    showProductInfo(data.product);
                    hideError();
                } else {
                    showError(data.message);
                    hideProductInfo();
                }
            })
            .catch(error => {
                showError('Terjadi kesalahan saat scanning');
                console.error(error);
            });
        }

        function checkout() {
            if (!currentProduct) return;

            const quantity = parseInt(document.getElementById('checkoutQuantity').value);
            if (quantity < 1 || quantity > currentProduct.at_distributor) {
                showError('Jumlah tidak valid');
                return;
            }

            fetch('{{ route("distributor.stock-out.checkout") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    serial_number: currentProduct.serial_number,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess(data.message);
                    resetForm();
                } else {
                    showError(data.message);
                }
            })
            .catch(error => {
                showError('Terjadi kesalahan saat checkout');
                console.error(error);
            });
        }

        function showProductInfo(product) {
            document.getElementById('productSerial').textContent = product.serial_number;
            document.getElementById('productProject').textContent = product.project_name;
            document.getElementById('productStock').textContent = product.at_distributor;
            document.getElementById('productInfo').classList.remove('hidden');
        }

        function hideProductInfo() {
            document.getElementById('productInfo').classList.add('hidden');
        }

        function showSuccess(message) {
            document.getElementById('successMessage').textContent = message;
            document.getElementById('alertSuccess').classList.remove('hidden');
            setTimeout(() => {
                document.getElementById('alertSuccess').classList.add('hidden');
            }, 3000);
        }

        function showError(message) {
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('alertError').classList.remove('hidden');
        }

        function hideError() {
            document.getElementById('alertError').classList.add('hidden');
        }

        function resetForm() {
            document.getElementById('serialNumberInput').value = '';
            document.getElementById('checkoutQuantity').value = '1';
            currentProduct = null;
            hideProductInfo();
            document.getElementById('serialNumberInput').focus();
        }
    </script>
</x-app-layout>

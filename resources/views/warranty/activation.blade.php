<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi Warranty - {{ session('retail_activation_name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">{{ session('retail_activation_name') }}</h1>
                    <p class="text-sm text-gray-600">Aktivasi Warranty</p>
                </div>
                <a href="{{ route('warranty.activation.logout') }}" class="text-red-600 hover:text-red-700">Logout</a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Aktivasi Warranty</h2>
            <div class="space-y-6">
                <!-- Tab Selection -->
                <div class="flex border-b border-gray-200">
                    <button onclick="showTab('scan')" id="scanTab" class="flex-1 py-3 px-4 text-center font-medium border-b-2 border-green-600 text-green-600">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        Scan QR
                    </button>
                    <button onclick="showTab('manual')" id="manualTab" class="flex-1 py-3 px-4 text-center font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Input Manual
                    </button>
                </div>

                <!-- Scan Tab -->
                <div id="scanContent">
                    <div class="mb-4">
                        <video id="video" class="w-full rounded-lg border-2 border-gray-300" style="max-height: 400px; object-fit: cover;"></video>
                        <canvas id="canvas" hidden></canvas>
                    </div>
                    <p class="text-sm text-gray-600 text-center">Arahkan kamera ke QR code produk</p>
                </div>

                <!-- Manual Tab -->
                <div id="manualContent" class="hidden">
                    <div>
                        <label for="serialNumberInput" class="block text-sm font-medium text-gray-700 mb-2">
                            Serial Number
                        </label>
                        <input 
                            type="text" 
                            id="serialNumberInput" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Masukkan serial number..." 
                        >
                    </div>

                    <button 
                        onclick="activateWarranty()" 
                        class="w-full px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition mt-4"
                    >
                        Aktivasi Warranty
                    </button>
                </div>

                <!-- Alert Success -->
                <div id="alertSuccess" class="hidden p-4 bg-green-100 text-green-800 rounded-lg">
                    <p class="font-medium" id="successMessage"></p>
                    <div id="warrantyDetails" class="mt-2 text-sm"></div>
                </div>

                <!-- Alert Error -->
                <div id="alertError" class="hidden p-4 bg-red-100 text-red-800 rounded-lg">
                    <p id="errorMessage"></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/@zxing/library@latest"></script>
    <script>
        let codeReader;
        let currentTab = 'scan';
        let isProcessing = false;

        function showTab(tab) {
            currentTab = tab;
            if (tab === 'scan') {
                document.getElementById('scanContent').classList.remove('hidden');
                document.getElementById('manualContent').classList.add('hidden');
                document.getElementById('scanTab').classList.add('border-green-600', 'text-green-600');
                document.getElementById('scanTab').classList.remove('border-transparent', 'text-gray-500');
                document.getElementById('manualTab').classList.remove('border-green-600', 'text-green-600');
                document.getElementById('manualTab').classList.add('border-transparent', 'text-gray-500');
                startScanning();
            } else {
                document.getElementById('scanContent').classList.add('hidden');
                document.getElementById('manualContent').classList.remove('hidden');
                document.getElementById('manualTab').classList.add('border-green-600', 'text-green-600');
                document.getElementById('manualTab').classList.remove('border-transparent', 'text-gray-500');
                document.getElementById('scanTab').classList.remove('border-green-600', 'text-green-600');
                document.getElementById('scanTab').classList.add('border-transparent', 'text-gray-500');
                stopScanning();
                document.getElementById('serialNumberInput').focus();
            }
        }

        function startScanning() {
            isProcessing = false;
            if (!codeReader) {
                codeReader = new ZXing.BrowserMultiFormatReader();
            }
            
            codeReader.decodeFromVideoDevice(undefined, 'video', (result, err) => {
                if (result && currentTab === 'scan' && !isProcessing) {
                    isProcessing = true;
                    console.log('Scanned:', result.text);
                    processSerialNumber(result.text);
                }
            });
        }

        function stopScanning() {
            if (codeReader) {
                codeReader.reset();
            }
        }

        // Initialize scan tab on load
        window.onload = function() {
            startScanning();
        };

        document.getElementById('serialNumberInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                activateWarranty();
            }
        });

        function activateWarranty() {
            const serialNumber = document.getElementById('serialNumberInput').value.trim();
            processSerialNumber(serialNumber);
        }

        function processSerialNumber(serialNumber) {
            if (!serialNumber) {
                showError('Masukkan serial number');
                isProcessing = false;
                return;
            }

            console.log('Processing serial number:', serialNumber);

            fetch('{{ route("warranty.activate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ serial_number: serialNumber })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response:', data);
                if (data.success) {
                    showSuccess(data.message, data.data);
                    document.getElementById('serialNumberInput').value = '';
                    setTimeout(() => { isProcessing = false; }, 2000);
                } else {
                    showError(data.message);
                    setTimeout(() => { isProcessing = false; }, 2000);
                }
            })
            .catch(error => {
                showError('Terjadi kesalahan saat aktivasi warranty');
                console.error('Error:', error);
                setTimeout(() => { isProcessing = false; }, 2000);
            });
        }

        function showSuccess(message, data) {
            document.getElementById('successMessage').textContent = message;
            
            if (data) {
                const details = `
                    <div class="space-y-1">
                        <div><strong>Serial Number:</strong> ${data.serial_number}</div>
                        <div><strong>Project:</strong> ${data.project_name}</div>
                        <div><strong>Diaktifkan:</strong> ${data.activated_at}</div>
                        <div><strong>Berakhir:</strong> ${data.warranty_expires_at}</div>
                    </div>
                `;
                document.getElementById('warrantyDetails').innerHTML = details;
            }
            
            document.getElementById('alertSuccess').classList.remove('hidden');
            document.getElementById('alertError').classList.add('hidden');
            
            setTimeout(() => {
                document.getElementById('alertSuccess').classList.add('hidden');
            }, 8000);
        }

        function showError(message) {
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('alertError').classList.remove('hidden');
            document.getElementById('alertSuccess').classList.add('hidden');
            
            setTimeout(() => {
                document.getElementById('alertError').classList.add('hidden');
            }, 5000);
        }
    </script>
</body>
</html>

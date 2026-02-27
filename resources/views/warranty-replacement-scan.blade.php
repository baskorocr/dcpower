<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Replacement Product</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="max-w-2xl mx-auto px-4 py-8">
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Scan Replacement Product</h1>
                <a href="{{ route('warranty.replacement.index') }}" class="text-blue-600 hover:text-blue-700">← Back</a>
            </div>

            <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                <p class="text-sm text-gray-700"><strong>Claim:</strong> {{ $claim->claim_number }}</p>
                <p class="text-sm text-gray-700"><strong>Old Product:</strong> {{ $claim->product->serial_number }}</p>
            </div>

            <!-- Tab Navigation -->
            <div class="flex border-b mb-6">
                <button onclick="switchTab('camera')" id="cameraTab" class="flex-1 py-3 px-4 text-center font-semibold border-b-2 border-blue-500 text-blue-600">
                    📷 Camera
                </button>
                <button onclick="switchTab('manual')" id="manualTab" class="flex-1 py-3 px-4 text-center font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                    ⌨️ Manual Input
                </button>
            </div>

            <!-- Camera Tab -->
            <div id="cameraContent">
                <div id="reader" class="w-full mb-4 rounded-lg overflow-hidden"></div>
                <p class="text-sm text-gray-600 text-center">Position QR code within the frame</p>
            </div>

            <!-- Manual Tab -->
            <div id="manualContent" class="hidden">
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Serial Number</label>
                    <input type="text" id="manualSerial" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter serial number">
                </div>
                <button onclick="processManual()" class="w-full px-6 py-3 bg-blue-500 text-white font-bold rounded-lg hover:bg-blue-600">
                    Process Replacement
                </button>
            </div>

            <!-- Result Message -->
            <div id="resultMessage" class="hidden mt-6 p-4 rounded-lg"></div>
        </div>
    </div>

    <script>
        let html5QrCode;
        let isProcessing = false;

        function switchTab(tab) {
            if (tab === 'camera') {
                document.getElementById('cameraTab').className = 'flex-1 py-3 px-4 text-center font-semibold border-b-2 border-blue-500 text-blue-600';
                document.getElementById('manualTab').className = 'flex-1 py-3 px-4 text-center font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-700';
                document.getElementById('cameraContent').classList.remove('hidden');
                document.getElementById('manualContent').classList.add('hidden');
                startScanner();
            } else {
                document.getElementById('cameraTab').className = 'flex-1 py-3 px-4 text-center font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-700';
                document.getElementById('manualTab').className = 'flex-1 py-3 px-4 text-center font-semibold border-b-2 border-blue-500 text-blue-600';
                document.getElementById('cameraContent').classList.add('hidden');
                document.getElementById('manualContent').classList.remove('hidden');
                stopScanner();
            }
        }

        function startScanner() {
            if (html5QrCode) return;

            html5QrCode = new Html5Qrcode("reader");
            html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                onScanSuccess,
                onScanFailure
            ).catch(err => {
                console.error("Camera error:", err);
                showMessage('Camera access denied. Please use manual input.', 'error');
            });
        }

        function stopScanner() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    html5QrCode = null;
                }).catch(err => console.error(err));
            }
        }

        function onScanSuccess(decodedText) {
            if (isProcessing) return;
            isProcessing = true;
            processReplacement(decodedText);
        }

        function onScanFailure(error) {
            // Ignore scan failures
        }

        async function processManual() {
            const serialNumber = document.getElementById('manualSerial').value.trim();
            if (!serialNumber) {
                showMessage('Please enter serial number', 'error');
                return;
            }
            await processReplacement(serialNumber);
        }

        async function processReplacement(serialNumber) {
            try {
                const response = await fetch('{{ route("warranty.replacement.scan", $claim) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ serial_number: serialNumber })
                });

                const data = await response.json();

                if (data.success) {
                    showMessage(`✓ ${data.message}<br>Old: ${data.old_product}<br>New: ${data.new_product}`, 'success');
                    setTimeout(() => {
                        window.location.href = '{{ route("warranty.replacement.index") }}';
                    }, 2000);
                } else {
                    showMessage(data.message, 'error');
                    isProcessing = false;
                }
            } catch (error) {
                showMessage('Error: ' + error.message, 'error');
                isProcessing = false;
            }
        }

        function showMessage(message, type) {
            const msgDiv = document.getElementById('resultMessage');
            msgDiv.className = `mt-6 p-4 rounded-lg ${type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
            msgDiv.innerHTML = message;
            msgDiv.classList.remove('hidden');
        }

        // Start scanner on load
        window.addEventListener('load', () => {
            startScanner();
        });

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            stopScanner();
        });
    </script>
</body>
</html>

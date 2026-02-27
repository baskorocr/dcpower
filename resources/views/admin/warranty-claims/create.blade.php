<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Submit Warranty Claim</h2>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl border-2 border-emerald-100 dark:border-emerald-800">
            <form method="POST" action="{{ route('warranty-claims.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Product Serial Number *</label>
                    <div class="flex gap-2">
                        <input type="text" id="serial_number" name="serial_number" value="{{ old('serial_number') }}" required class="flex-1 px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2" placeholder="Enter or scan serial number">
                        <button type="button" onclick="startScanner()" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                            </svg>
                            Scan
                        </button>
                    </div>
                    @error('serial_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    
                    <!-- Validation Status -->
                    <div id="serial-status" class="mt-2 hidden">
                        <div id="status-message" class="p-3 rounded-lg text-sm font-medium"></div>
                        <div id="product-info" class="mt-2 text-xs text-gray-600 dark:text-gray-400"></div>
                    </div>
                    
                    <!-- Scanner Container -->
                    <div id="scanner-container" class="hidden mt-4">
                        <div id="reader" class="border-2 border-emerald-200 rounded-lg overflow-hidden"></div>
                        <button type="button" onclick="stopScanner()" class="mt-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Stop Scanner
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nomor WhatsApp *</label>
                    <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number') }}" required class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2" placeholder="Contoh: 08123456789">
                    @error('whatsapp_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Pembelian *</label>
                    <select name="purchase_type" required class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2">
                        <option value="">Pilih jenis pembelian</option>
                        <option value="online" {{ old('purchase_type') == 'online' ? 'selected' : '' }}>Online</option>
                        <option value="offline" {{ old('purchase_type') == 'offline' ? 'selected' : '' }}>Offline</option>
                    </select>
                    @error('purchase_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tanggal Pembelian *</label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date') }}" required class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2">
                    @error('purchase_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tanggal Baterai Bermasalah *</label>
                    <input type="date" name="battery_issue_date" value="{{ old('battery_issue_date') }}" required class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2">
                    @error('battery_issue_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Complaint Type *</label>
                    <select name="complaint_type" required class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2">
                        <option value="">Select type</option>
                        <option value="defect" {{ old('complaint_type') == 'defect' ? 'selected' : '' }}>Defect</option>
                        <option value="damage" {{ old('complaint_type') == 'damage' ? 'selected' : '' }}>Damage</option>
                        <option value="malfunction" {{ old('complaint_type') == 'malfunction' ? 'selected' : '' }}>Malfunction</option>
                        <option value="other" {{ old('complaint_type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('complaint_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Description *</label>
                    <textarea name="complaint_description" rows="4" required class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2" placeholder="Describe the issue in detail...">{{ old('complaint_description') }}</textarea>
                    @error('complaint_description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tipe Motor *</label>
                    <input type="text" name="motor_type" value="{{ old('motor_type') }}" required class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2" placeholder="Contoh: Honda Beat, Yamaha Mio, dll">
                    @error('motor_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Apakah Pernah Modifikasi? *</label>
                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="has_modification" value="0" {{ old('has_modification', '0') == '0' ? 'checked' : '' }} onchange="toggleModifications()" class="mr-2">
                            <span>Tidak</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="has_modification" value="1" {{ old('has_modification') == '1' ? 'checked' : '' }} onchange="toggleModifications()" class="mr-2">
                            <span>Ya</span>
                        </label>
                    </div>
                    @error('has_modification')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div id="modification-types" class="hidden">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Jenis Modifikasi</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="modification_types[]" value="boreup" {{ in_array('boreup', old('modification_types', [])) ? 'checked' : '' }} class="mr-2">
                            <span>Boreup</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="modification_types[]" value="ganti_kiprok" {{ in_array('ganti_kiprok', old('modification_types', [])) ? 'checked' : '' }} class="mr-2">
                            <span>Ganti Kiprok</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="modification_types[]" value="ganti_spull" {{ in_array('ganti_spull', old('modification_types', [])) ? 'checked' : '' }} class="mr-2">
                            <span>Ganti Spull</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="modification_types[]" value="ganti_coil" {{ in_array('ganti_coil', old('modification_types', [])) ? 'checked' : '' }} class="mr-2">
                            <span>Ganti Coil</span>
                        </label>
                    </div>
                    @error('modification_types')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Photo Evidence *</label>
                    <input type="file" name="photo_evidence" accept="image/jpeg,image/jpg,image/png" required class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2">
                    <p class="text-xs text-gray-500 mt-1">Upload clear photo of the defect/damage (JPEG/PNG only, max 5MB, min 400x400px)</p>
                    @error('photo_evidence')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    <div id="preview" class="mt-2"></div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold rounded-lg hover:scale-105 transition-transform">
                        Submit Claim
                    </button>
                    <a href="{{ route('warranty-claims.index') }}" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                        Cancel
                    </a>
                </div>
            </form>
            
            <script src="https://unpkg.com/html5-qrcode"></script>
            <script>
                let html5QrCode;
                let isSerialValid = false;
                
                function toggleModifications() {
                    const hasModification = document.querySelector('input[name="has_modification"]:checked').value;
                    const modificationTypes = document.getElementById('modification-types');
                    
                    if (hasModification === '1') {
                        modificationTypes.classList.remove('hidden');
                    } else {
                        modificationTypes.classList.add('hidden');
                        document.querySelectorAll('input[name="modification_types[]"]').forEach(cb => cb.checked = false);
                    }
                }
                
                function checkSerialNumber(serialNumber) {
                    if (!serialNumber) {
                        document.getElementById('serial-status').classList.add('hidden');
                        return;
                    }
                    
                    fetch('{{ route("warranty-claims.check-serial") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ serial_number: serialNumber })
                    })
                    .then(response => response.json())
                    .then(data => {
                        const statusDiv = document.getElementById('serial-status');
                        const messageDiv = document.getElementById('status-message');
                        const infoDiv = document.getElementById('product-info');
                        
                        statusDiv.classList.remove('hidden');
                        isSerialValid = data.valid;
                        
                        if (data.status === 'unknown') {
                            messageDiv.className = 'p-3 rounded-lg text-sm font-medium bg-red-100 text-red-800 border-2 border-red-300';
                            messageDiv.textContent = '❌ ' + data.message;
                            infoDiv.innerHTML = '';
                        } else if (data.status === 'expired') {
                            messageDiv.className = 'p-3 rounded-lg text-sm font-medium bg-red-100 text-red-800 border-2 border-red-300';
                            messageDiv.textContent = '❌ ' + data.message;
                            infoDiv.innerHTML = '<span class="font-semibold">Status: </span>' + data.product_status;
                        } else if (data.status === 'claimed') {
                            messageDiv.className = 'p-3 rounded-lg text-sm font-medium bg-red-100 text-red-800 border-2 border-red-300';
                            messageDiv.textContent = '❌ ' + data.message;
                            infoDiv.innerHTML = '<span class="font-semibold">Status: </span>' + data.product_status;
                        } else if (data.status === 'rejected') {
                            messageDiv.className = 'p-3 rounded-lg text-sm font-medium bg-red-100 text-red-800 border-2 border-red-300';
                            messageDiv.textContent = '❌ ' + data.message;
                            infoDiv.innerHTML = '<span class="font-semibold">Status: </span>' + data.product_status;
                        } else if (data.status === 'not_activated') {
                            messageDiv.className = 'p-3 rounded-lg text-sm font-medium bg-yellow-100 text-yellow-800 border-2 border-yellow-300';
                            messageDiv.textContent = '⚠️ ' + data.message;
                            infoDiv.innerHTML = '<span class="font-semibold">Status: </span>' + data.product_status;
                        } else if (data.status === 'genuine') {
                            messageDiv.className = 'p-3 rounded-lg text-sm font-medium bg-green-100 text-green-800 border-2 border-green-300';
                            messageDiv.textContent = '✅ ' + data.message;
                            infoDiv.innerHTML = '<span class="font-semibold">Status: </span>' + data.product_status + '<br>' +
                                '<span class="font-semibold">Product: </span>' + data.product.name + '<br>' +
                                '<span class="font-semibold">Activated: </span>' + data.product.activated_at + '<br>' +
                                '<span class="font-semibold">Expires: </span>' + data.product.expires_at;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                }
                
                document.getElementById('serial_number').addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        checkSerialNumber(this.value);
                    }
                });
                
                document.getElementById('serial_number').addEventListener('blur', function() {
                    checkSerialNumber(this.value);
                });
                
                document.querySelector('form').addEventListener('submit', function(e) {
                    const serialNumber = document.getElementById('serial_number').value;
                    if (!serialNumber) {
                        e.preventDefault();
                        alert('Serial number harus diisi.');
                        return false;
                    }
                    if (!isSerialValid) {
                        e.preventDefault();
                        alert('Serial number tidak valid. Pastikan produk asli, warranty sudah diaktivasi, dan tidak berstatus warranty_expired, product_claim, atau claim_rejected.');
                        return false;
                    }
                });
                
                document.addEventListener('DOMContentLoaded', function() {
                    toggleModifications();
                });
                
                function startScanner() {
                    document.getElementById('scanner-container').classList.remove('hidden');
                    
                    html5QrCode = new Html5Qrcode("reader");
                    html5QrCode.start(
                        { facingMode: "environment" },
                        { fps: 10, qrbox: 250 },
                        (decodedText) => {
                            document.getElementById('serial_number').value = decodedText;
                            checkSerialNumber(decodedText);
                            stopScanner();
                        }
                    ).catch(err => {
                        console.error(err);
                        alert('Unable to start camera. Please enter serial number manually.');
                        stopScanner();
                    });
                }
                
                function stopScanner() {
                    if (html5QrCode) {
                        html5QrCode.stop().then(() => {
                            document.getElementById('scanner-container').classList.add('hidden');
                        }).catch(err => console.error(err));
                    }
                }
                
                document.querySelector('input[name="photo_evidence"]').addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    const preview = document.getElementById('preview');
                    
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            preview.innerHTML = '<img src="' + e.target.result + '" class="max-w-xs rounded-lg border-2 border-emerald-200">';
                        }
                        reader.readAsDataURL(file);
                    }
                });
            </script>
        </div>
    </div>
</x-app-layout>

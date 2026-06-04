<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Ajukan Klaim Garansi</h2>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl border-2 border-emerald-100 dark:border-emerald-800">
            <form method="POST" action="{{ route('warranty-claims.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nomor Seri Produk *</label>
                    <div class="flex gap-2">
                        <input type="text" id="serial_number" name="serial_number" value="{{ old('serial_number') }}" required class="flex-1 px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2" placeholder="Masukkan atau scan nomor seri">
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
                            Hentikan Scanner
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nomor WhatsApp *</label>
                    <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number') }}" required class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2" placeholder="Contoh: 08123456789">
                    @error('whatsapp_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Alamat *</label>
                    <textarea name="address" rows="2" required class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2" placeholder="Alamat lengkap">{{ old('address') }}</textarea>
                    @error('address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Kota *</label>
                    <input type="text" name="city" value="{{ old('city') }}" required class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2" placeholder="Contoh: Jakarta">
                    @error('city')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Provinsi *</label>
                    <input type="text" name="province" value="{{ old('province') }}" required class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2" placeholder="Contoh: DKI Jakarta">
                    @error('province')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
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
                    <input type="date" name="purchase_date" value="{{ old('purchase_date') }}" max="{{ date('Y-m-d') }}" required class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2">
                    @error('purchase_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tanggal Baterai Bermasalah *</label>
                    <input type="date" name="battery_issue_date" value="{{ old('battery_issue_date') }}" max="{{ date('Y-m-d') }}" required class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2">
                    @error('battery_issue_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Jenis Keluhan *</label>
                    <select name="complaint_type" required class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2">
                        <option value="">Pilih jenis</option>
                        <option value="defect" {{ old('complaint_type') == 'defect' ? 'selected' : '' }}>Cacat Produk</option>
                        <option value="damage" {{ old('complaint_type') == 'damage' ? 'selected' : '' }}>Rusak</option>
                        <option value="malfunction" {{ old('complaint_type') == 'malfunction' ? 'selected' : '' }}>Tidak Berfungsi</option>
                        <option value="other" {{ old('complaint_type') == 'other' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('complaint_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Deskripsi * (minimal 10 kata)</label>
                    <textarea name="complaint_description" rows="4" required class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2" placeholder="Jelaskan masalahnya secara detail...">{{ old('complaint_description') }}</textarea>
                    @error('complaint_description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tipe Motor *</label>
                    <input type="text" name="motor_type" value="{{ old('motor_type') }}" required class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2" placeholder="Contoh: Honda Beat, Yamaha Mio, dll">
                    @error('motor_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tahun Motor *</label>
                    <input type="number" name="motor_year" value="{{ old('motor_year') }}" required min="1900" max="{{ date('Y') }}" class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2" placeholder="Contoh: 2020">
                    @error('motor_year')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
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
                            <input type="radio" name="modification_type" value="boreup" {{ old('modification_type') == 'boreup' ? 'checked' : '' }} onchange="toggleOtherModification()" class="mr-2">
                            <span>Boreup</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="modification_type" value="ganti_kiprok" {{ old('modification_type') == 'ganti_kiprok' ? 'checked' : '' }} onchange="toggleOtherModification()" class="mr-2">
                            <span>Ganti Kiprok</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="modification_type" value="ganti_spull" {{ old('modification_type') == 'ganti_spull' ? 'checked' : '' }} onchange="toggleOtherModification()" class="mr-2">
                            <span>Ganti Spull</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="modification_type" value="ganti_coil" {{ old('modification_type') == 'ganti_coil' ? 'checked' : '' }} onchange="toggleOtherModification()" class="mr-2">
                            <span>Ganti Coil</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="modification_type" value="other" {{ old('modification_type') == 'other' ? 'checked' : '' }} onchange="toggleOtherModification()" class="mr-2">
                            <span>Other</span>
                        </label>
                    </div>
                    @error('modification_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    
                    <div id="other-modification" class="hidden mt-3">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Sebutkan Modifikasi Lainnya</label>
                        <input type="text" name="modification_other" value="{{ old('modification_other') }}" class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2" placeholder="Jelaskan modifikasi lainnya">
                        @error('modification_other')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Foto Seal *</label>
                    <input type="file" name="photo_evidence" accept="image/jpeg,image/jpg,image/png" required class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2">
                    <p class="text-xs text-gray-500 mt-1">Upload foto seal yang jelas (JPEG/PNG, max 5MB, min 400x400px)</p>
                    @error('photo_evidence')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    <div id="preview" class="mt-2"></div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Foto Kerusakan *</label>
                    <input type="file" name="photo_damage" accept="image/jpeg,image/jpg,image/png" required class="w-full px-4 py-2 border-2 border-emerald-200 dark:border-emerald-700 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-2">
                    <p class="text-xs text-gray-500 mt-1">Upload foto kerusakan yang jelas (JPEG/PNG, max 5MB, min 400x400px)</p>
                    @error('photo_damage')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    <div id="preview_damage" class="mt-2"></div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold rounded-lg hover:scale-105 transition-transform">
                        Ajukan Klaim
                    </button>
                    <a href="{{ route('warranty-claims.index') }}" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                        Batal
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
                        document.querySelectorAll('input[name="modification_type"]').forEach(rb => rb.checked = false);
                        document.getElementById('other-modification').classList.add('hidden');
                    }
                }
                
                function toggleOtherModification() {
                    const selectedModification = document.querySelector('input[name="modification_type"]:checked');
                    const otherModificationDiv = document.getElementById('other-modification');
                    
                    if (selectedModification && selectedModification.value === 'other') {
                        otherModificationDiv.classList.remove('hidden');
                    } else {
                        otherModificationDiv.classList.add('hidden');
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
                                '<span class="font-semibold">Produk: </span>' + data.product.name + '<br>' +
                                '<span class="font-semibold">Diaktivasi: </span>' + data.product.activated_at + '<br>' +
                                '<span class="font-semibold">Kadaluarsa: </span>' + data.product.expires_at;
                            
                            // Auto-fill purchase date with activation date
                            const purchaseDateInput = document.querySelector('input[name="purchase_date"]');
                            if (data.product.activated_at) {
                                // Convert DD/MM/YYYY to YYYY-MM-DD
                                const parts = data.product.activated_at.split('/');
                                if (parts.length === 3) {
                                    const formattedDate = parts[2] + '-' + parts[1] + '-' + parts[0];
                                    purchaseDateInput.value = formattedDate;
                                    purchaseDateInput.readOnly = true;
                                    purchaseDateInput.classList.add('bg-gray-100', 'cursor-not-allowed');
                                }
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                }
                
                let typingTimer;
                const typingDelay = 800; // 800ms after user stops typing

                document.getElementById('serial_number').addEventListener('input', function() {
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(() => {
                        checkSerialNumber(this.value);
                    }, typingDelay);
                });

                document.getElementById('serial_number').addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        clearTimeout(typingTimer);
                        checkSerialNumber(this.value);
                    }
                });
                
                document.getElementById('serial_number').addEventListener('blur', function() {
                    clearTimeout(typingTimer);
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
                    toggleOtherModification();
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
                        alert('Tidak dapat memulai kamera. Silakan masukkan nomor seri secara manual.');
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

                document.querySelector('input[name="photo_damage"]').addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    const preview = document.getElementById('preview_damage');
                    
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            preview.innerHTML = '<img src="' + e.target.result + '" class="max-w-xs rounded-lg border-2 border-emerald-200">';
                        }
                        reader.readAsDataURL(file);
                    }
                });

                // Date validation
                const purchaseDate = document.querySelector('input[name="purchase_date"]');
                const issueDate = document.querySelector('input[name="battery_issue_date"]');
                
                purchaseDate.addEventListener('change', function() {
                    issueDate.min = this.value;
                    if (issueDate.value && issueDate.value < this.value) {
                        issueDate.value = '';
                    }
                });
                
                issueDate.addEventListener('change', function() {
                    if (purchaseDate.value && this.value < purchaseDate.value) {
                        alert('Tanggal baterai bermasalah tidak boleh lebih awal dari tanggal pembelian');
                        this.value = '';
                    }
                });
            </script>
        </div>
    </div>
</x-app-layout>

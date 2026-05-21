<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Products</h2>
            <div class="flex gap-2">
                @if(!auth()->user()->hasRole(['distributor', 'buyer']))
                <a href="{{ route('products.switch') }}" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-colors">
                    Switch Serial Number
                </a>
                <a href="{{ route('products.create') }}" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-lg hover:scale-105 transition-transform">
                    + New Product
                </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl border-2 border-emerald-100 dark:border-emerald-800">
        @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-50 dark:bg-emerald-900/20 border-l-4 border-emerald-500 text-emerald-700 dark:text-emerald-400">
            {{ session('success') }}
        </div>
        @endif

        <!-- Search & Filter -->
        <form method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search serial number..." class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-1 dark:text-gray-200">
            </div>
            
            <div>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-1 dark:text-gray-200">
                    <option value="">All Status</option>
                    <option value="manufactured" {{ request('status') == 'manufactured' ? 'selected' : '' }}>Manufactured</option>
                    <option value="in_distributor" {{ request('status') == 'in_distributor' ? 'selected' : '' }}>In Distributor</option>
                    <option value="retail" {{ request('status') == 'retail' ? 'selected' : '' }}>Retail</option>
                    <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Sold</option>
                    <option value="product_claim" {{ request('status') == 'product_claim' ? 'selected' : '' }}>Product Claim</option>
                    <option value="warranty_expired" {{ request('status') == 'warranty_expired' ? 'selected' : '' }}>Warranty Expired</option>
                </select>
            </div>
            
            <div>
                <select name="project_id" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-1 dark:text-gray-200">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            
            @if($variants->isNotEmpty())
            <div>
                <select name="variant" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-1 dark:text-gray-200">
                    <option value="">All Variants</option>
                    @foreach($variants as $variant)
                    <option value="{{ $variant }}" {{ request('variant') == $variant ? 'selected' : '' }}>{{ $variant }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            
            <div>
                <select name="per_page" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-dark-eval-1 dark:text-gray-200">
                    <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20 per page</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per page</option>
                    <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200 per page</option>
                </select>
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                    Filter
                </button>
                <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    Reset
                </a>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b-2 border-gray-200 dark:border-gray-700">
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                            <input type="checkbox" id="select-all" class="rounded">
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Serial Number</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Variant</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Packing Code</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Project</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Created</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-4 py-3">
                            <input type="checkbox" class="product-checkbox rounded" value="{{ $product->id }}" data-packing="{{ $product->standard_packing_id }}">
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-mono text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $product->serial_number }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $product->variant ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @if($product->standardPacking)
                            <a href="{{ route('standard-packings.show', $product->standardPacking) }}" class="font-mono text-sm font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                {{ $product->standardPacking->packing_code }}
                            </a>
                            @else
                            <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                            {{ $product->project->name }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col gap-1">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($product->status === 'manufactured') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                    @elseif($product->status === 'sold') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                    @elseif($product->status === 'warranty_expired') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $product->status)) }}
                                </span>
                                @if($product->status === 'warranty_expired' && $product->can_repair)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                                    Can Repair
                                </span>
                                @if($product->repairDistributor)
                                <span class="text-xs text-gray-600 dark:text-gray-400">
                                    → {{ $product->repairDistributor->name }}
                                </span>
                                @endif
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                            {{ $product->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-700 text-sm font-semibold">
                                    View
                                </a>
                                @if($product->status === 'warranty_expired' && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('marketing')))
                                <button onclick="openRepairModal({{ $product->id }}, '{{ $product->serial_number }}', {{ $product->can_repair ? 'true' : 'false' }}, {{ $product->repair_distributor_id ?? 'null' }})" class="text-purple-600 hover:text-purple-700 text-sm font-semibold">
                                    Edit Status
                                </button>
                                @endif
                                @if(!auth()->user()->hasRole(['distributor', 'buyer']))
                                <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-semibold">
                                        Delete
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">No products found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Bulk Actions -->
        <div id="bulk-actions" class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hidden">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    <span id="selected-count">0</span> item(s) selected
                </span>
                <div class="flex gap-2">
                    <button onclick="printSelected()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
                        Print Product Labels
                    </button>
                    @if(auth()->user()->hasAnyRole(['QA', 'admin']))
                    <button onclick="printSelectedPackings()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                        Print Packing Labels
                    </button>
                    @endif
                    @if(!auth()->user()->hasRole(['distributor', 'buyer']))
                    <button onclick="deleteSelected()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium">
                        Delete Selected
                    </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </div>

    <!-- Repair Status Modal -->
    <div id="repairModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white dark:bg-dark-eval-1 rounded-2xl p-6 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">Edit Repair Status</h3>
                <button onclick="closeRepairModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="repairForm" onsubmit="submitRepairStatus(event)">
                <input type="hidden" id="productId" name="product_id">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Serial Number
                    </label>
                    <input type="text" id="serialNumber" readonly class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Can be Repaired?
                    </label>
                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="can_repair" value="1" onchange="toggleDistributorField()" class="mr-2">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Yes</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="can_repair" value="0" onchange="toggleDistributorField()" class="mr-2">
                            <span class="text-sm text-gray-700 dark:text-gray-300">No</span>
                        </label>
                    </div>
                </div>

                <div id="distributorField" class="mb-4 hidden">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Send to Distributor <span class="text-red-500">*</span>
                    </label>
                    <select name="repair_distributor_id" id="repairDistributorId" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:bg-dark-eval-1 dark:text-gray-200">
                        <option value="">Select Distributor</option>
                        @foreach(\App\Models\Distributor::where('status', 'active')->get() as $distributor)
                        <option value="{{ $distributor->id }}">{{ $distributor->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2 justify-end">
                    <button type="button" onclick="closeRepairModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                        Save Status
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.product-checkbox');
        const bulkActions = document.getElementById('bulk-actions');
        const selectedCount = document.getElementById('selected-count');

        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkActions();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkActions);
        });

        function updateBulkActions() {
            const checked = document.querySelectorAll('.product-checkbox:checked');
            selectedCount.textContent = checked.length;
            
            if (checked.length > 0) {
                bulkActions.classList.remove('hidden');
            } else {
                bulkActions.classList.add('hidden');
            }
        }

        function printSelected() {
            const checked = document.querySelectorAll('.product-checkbox:checked');
            const productIds = Array.from(checked).map(cb => cb.value);
            
            if (productIds.length === 0) {
                alert('No products selected');
                return;
            }

            window.open(`/products/print?ids=${productIds.join(',')}`, '_blank');
        }

        function printSelectedPackings() {
            const checked = document.querySelectorAll('.product-checkbox:checked');
            const packingIds = new Set();
            
            checked.forEach(cb => {
                const packingId = cb.dataset.packing;
                if (packingId && packingId !== 'null') {
                    packingIds.add(packingId);
                }
            });

            if (packingIds.size === 0) {
                alert('No packing codes found for selected products');
                return;
            }

            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/standard-packings/print-multiple';
            form.target = '_blank';
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);
            
            const idsInput = document.createElement('input');
            idsInput.type = 'hidden';
            idsInput.name = 'ids';
            idsInput.value = Array.from(packingIds).join(',');
            form.appendChild(idsInput);
            
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }

        function deleteSelected() {
            const checked = document.querySelectorAll('.product-checkbox:checked');
            const productIds = Array.from(checked).map(cb => cb.value);
            
            if (productIds.length === 0) {
                alert('No products selected');
                return;
            }

            if (!confirm(`Are you sure you want to delete ${productIds.length} product(s)?`)) {
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("products.bulk-delete") }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            productIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'product_ids[]';
                input.value = id;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }

        // Repair Status Modal Functions
        function openRepairModal(productId, serialNumber, canRepair, distributorId, notes) {
            document.getElementById('productId').value = productId;
            document.getElementById('serialNumber').value = serialNumber;
            
            // Set radio button
            const radioYes = document.querySelector('input[name="can_repair"][value="1"]');
            const radioNo = document.querySelector('input[name="can_repair"][value="0"]');
            
            if (canRepair) {
                radioYes.checked = true;
                document.getElementById('distributorField').classList.remove('hidden');
                if (distributorId) {
                    document.getElementById('repairDistributorId').value = distributorId;
                }
            } else {
                radioNo.checked = true;
                document.getElementById('distributorField').classList.add('hidden');
            }
            
            document.getElementById('repairModal').classList.remove('hidden');
            document.getElementById('repairModal').classList.add('flex');
        }

        function closeRepairModal() {
            document.getElementById('repairModal').classList.add('hidden');
            document.getElementById('repairModal').classList.remove('flex');
            document.getElementById('repairForm').reset();
        }

        function toggleDistributorField() {
            const canRepair = document.querySelector('input[name="can_repair"]:checked').value;
            const distributorField = document.getElementById('distributorField');
            const distributorSelect = document.getElementById('repairDistributorId');
            
            if (canRepair === '1') {
                distributorField.classList.remove('hidden');
                distributorSelect.required = true;
            } else {
                distributorField.classList.add('hidden');
                distributorSelect.required = false;
                distributorSelect.value = '';
            }
        }

        async function submitRepairStatus(event) {
            event.preventDefault();
            
            const productId = document.getElementById('productId').value;
            const canRepair = document.querySelector('input[name="can_repair"]:checked').value;
            const distributorId = document.getElementById('repairDistributorId').value;
            
            // Validate
            if (canRepair === '1' && !distributorId) {
                alert('Please select a distributor');
                return;
            }
            
            try {
                const response = await fetch(`/products/${productId}/repair-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        can_repair: canRepair === '1',
                        repair_distributor_id: distributorId || null
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    closeRepairModal();
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to update repair status');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while updating repair status');
            }
        }
    </script>
</x-app-layout>

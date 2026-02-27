<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                    {{ __('Dashboard') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Welcome back! Here\'s what\'s happening today.') }}
                </p>
            </div>
        </div>
    </x-slot>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-5 mb-6 sm:grid-cols-2 lg:grid-cols-5">
        <!-- Stock Manufactured -->
        <div class="relative overflow-hidden p-6 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-2xl border-2 border-blue-200 dark:border-blue-700">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider">{{ __('Manufactured') }}</p>
                    <p class="mt-3 text-4xl font-black text-blue-900 dark:text-blue-100">{{ number_format($stats['stock_manufactured']) }}</p>
                </div>
                <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Stock at Distributor -->
        <div class="relative overflow-hidden p-6 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-2xl border-2 border-green-200 dark:border-green-700 cursor-pointer hover:scale-105 transition-transform" onclick="showDistributorModal()">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-green-600 dark:text-green-400 uppercase tracking-wider">{{ __('At Distributor') }}</p>
                    <p class="mt-3 text-4xl font-black text-green-900 dark:text-green-100">{{ number_format($stats['stock_distributor']) }}</p>
                </div>
                <div class="p-3 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Stock at Retail -->
        <div class="relative overflow-hidden p-6 bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-2xl border-2 border-purple-200 dark:border-purple-700 cursor-pointer hover:scale-105 transition-transform" onclick="showRetailModal()">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-purple-600 dark:text-purple-400 uppercase tracking-wider">{{ __('At Retail') }}</p>
                    <p class="mt-3 text-4xl font-black text-purple-900 dark:text-purple-100">{{ number_format($stats['stock_retail']) }}</p>
                </div>
                <div class="p-3 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Sold -->
        <div class="relative overflow-hidden p-6 bg-gradient-to-br from-cyan-50 to-sky-50 dark:from-cyan-900/20 dark:to-sky-900/20 rounded-2xl border-2 border-cyan-200 dark:border-cyan-700">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-cyan-600 dark:text-cyan-400 uppercase tracking-wider">{{ __('Total Sold') }}</p>
                    <p class="mt-3 text-4xl font-black text-cyan-900 dark:text-cyan-100">{{ number_format($stats['total_sold']) }}</p>
                </div>
                <div class="p-3 bg-gradient-to-br from-cyan-500 to-sky-600 rounded-xl shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Claims -->
        <div class="relative overflow-hidden p-6 bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 rounded-2xl border-2 border-orange-200 dark:border-orange-700">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-orange-600 dark:text-orange-400 uppercase tracking-wider">{{ __('Pending Claims') }}</p>
                    <p class="mt-3 text-4xl font-black text-orange-900 dark:text-orange-100">{{ number_format($stats['pending_claims']) }}</p>
                </div>
                <div class="p-3 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Products with Filters -->
    <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl border-2 border-blue-100 dark:border-blue-800 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">Recent Products</h3>
            <a href="{{ route('products.index') }}" class="text-sm text-blue-600 hover:text-blue-700">View All →</a>
        </div>

        <!-- Filters -->
        <form method="GET" class="mb-4 flex gap-3">
            <select name="project" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-800 dark:text-gray-200" onchange="this.form.submit()">
                <option value="">All Projects</option>
                @foreach($projects as $project)
                <option value="{{ $project->id }}" {{ request('project') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                @endforeach
            </select>
            <select name="status" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-800 dark:text-gray-200" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="manufactured" {{ request('status') == 'manufactured' ? 'selected' : '' }}>Manufactured</option>
                <option value="in_distributor" {{ request('status') == 'in_distributor' ? 'selected' : '' }}>In Distributor</option>
                <option value="retail" {{ request('status') == 'retail' ? 'selected' : '' }}>Retail</option>
                <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Sold</option>
                <option value="claimed" {{ request('status') == 'claimed' ? 'selected' : '' }}>Claimed</option>
            </select>
            @if(request('project') || request('status'))
            <a href="{{ route('dashboard') }}" class="px-3 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-300">Clear</a>
            @endif
        </form>

        @if($products->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Serial Number</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Project</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-4 py-3">
                            <span class="font-mono text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $product->serial_number }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $product->project->name }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($product->status === 'manufactured') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                @elseif($product->status === 'sold') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                @else bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400
                                @endif">
                                {{ ucfirst($product->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                            {{ $product->created_at->diffForHumans() }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-8">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            <p class="text-gray-600 dark:text-gray-400">No products yet</p>
            <a href="{{ route('products.create') }}" class="mt-4 inline-block px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                Create First Product
            </a>
        </div>
        @endif
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-5 mb-6 sm:grid-cols-2 lg:grid-cols-2">
        <!-- Active Sessions -->
        <div class="relative overflow-hidden p-6 bg-gradient-to-br from-teal-50 to-emerald-50 dark:from-teal-900/20 dark:to-emerald-900/20 rounded-2xl border-2 border-teal-200 dark:border-teal-700">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-teal-600 dark:text-teal-400 uppercase tracking-wider">{{ __('System Status') }}</p>
                    <p class="mt-3 text-2xl font-black text-teal-900 dark:text-teal-100">{{ __('All Systems Operational') }}</p>
                    <p class="mt-3 text-xs font-bold text-teal-600 dark:text-teal-400">
                        ✓ {{ __('Running smoothly') }}
                    </p>
                </div>
                <div class="p-3 bg-gradient-to-br from-teal-500 to-emerald-600 rounded-xl shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Message -->
    <div class="relative overflow-hidden p-8 bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500 rounded-2xl shadow-lg">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full -ml-24 -mb-24"></div>
        <div class="relative flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-black text-white mb-2">{{ __('Welcome to DC Power Warranty System!') }} 🌿</h3>
                <p class="text-emerald-50 font-medium">{{ __('Everything is running smoothly. You\'re all set to manage your warranty claims.') }}</p>
            </div>
            <div class="hidden md:block">
                <svg class="w-20 h-20 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Distributor Stock Modal -->
    <div id="distributorModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-dark-eval-1 rounded-2xl max-w-4xl w-full max-h-[80vh] overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">Distributor Stock Details</h3>
                <button onclick="closeDistributorModal()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[calc(80vh-80px)]">
                <div id="distributorTableContent">
                    <div class="text-center py-8">
                        <svg class="animate-spin h-8 w-8 mx-auto text-green-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">Loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Retail Stock Modal -->
    <div id="retailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-dark-eval-1 rounded-2xl max-w-4xl w-full max-h-[80vh] overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">Retail Stock Details</h3>
                <button onclick="closeRetailModal()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[calc(80vh-80px)]">
                <div id="retailTableContent">
                    <div class="text-center py-8">
                        <svg class="animate-spin h-8 w-8 mx-auto text-purple-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">Loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showDistributorModal() {
            document.getElementById('distributorModal').classList.remove('hidden');
            fetch('{{ route('dashboard.distributor-stocks') }}')
                .then(res => res.json())
                .then(data => {
                    let html = '<table class="w-full"><thead class="bg-green-50 dark:bg-green-900/20"><tr>';
                    html += '<th class="px-4 py-3 text-left text-xs font-semibold text-green-600 dark:text-green-400 uppercase">Name</th>';
                    html += '<th class="px-4 py-3 text-left text-xs font-semibold text-green-600 dark:text-green-400 uppercase">Project</th>';
                    html += '<th class="px-4 py-3 text-left text-xs font-semibold text-green-600 dark:text-green-400 uppercase">City</th>';
                    html += '<th class="px-4 py-3 text-right text-xs font-semibold text-green-600 dark:text-green-400 uppercase">Stock</th>';
                    html += '</tr></thead><tbody class="divide-y divide-gray-200 dark:divide-gray-700">';
                    
                    data.forEach(dist => {
                        html += '<tr class="hover:bg-green-50 dark:hover:bg-green-900/10">';
                        html += `<td class="px-4 py-3 font-medium">${dist.name}</td>`;
                        html += `<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">${dist.project?.name || '-'}</td>`;
                        html += `<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">${dist.city || '-'}</td>`;
                        html += `<td class="px-4 py-3 text-right"><span class="px-3 py-1 text-xs font-bold rounded-full ${dist.stock_count < 10 ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800'}">${dist.stock_count || 0} units</span></td>`;
                        html += '</tr>';
                    });
                    
                    html += '</tbody></table>';
                    document.getElementById('distributorTableContent').innerHTML = html;
                });
        }

        function closeDistributorModal() {
            document.getElementById('distributorModal').classList.add('hidden');
        }

        function showRetailModal() {
            document.getElementById('retailModal').classList.remove('hidden');
            fetch('{{ route('dashboard.retail-stocks') }}')
                .then(res => res.json())
                .then(data => {
                    let html = '<table class="w-full"><thead class="bg-purple-50 dark:bg-purple-900/20"><tr>';
                    html += '<th class="px-4 py-3 text-left text-xs font-semibold text-purple-600 dark:text-purple-400 uppercase">Name</th>';
                    html += '<th class="px-4 py-3 text-left text-xs font-semibold text-purple-600 dark:text-purple-400 uppercase">Distributor</th>';
                    html += '<th class="px-4 py-3 text-left text-xs font-semibold text-purple-600 dark:text-purple-400 uppercase">City</th>';
                    html += '<th class="px-4 py-3 text-right text-xs font-semibold text-purple-600 dark:text-purple-400 uppercase">Stock</th>';
                    html += '</tr></thead><tbody class="divide-y divide-gray-200 dark:divide-gray-700">';
                    
                    data.forEach(retail => {
                        html += '<tr class="hover:bg-purple-50 dark:hover:bg-purple-900/10">';
                        html += `<td class="px-4 py-3 font-medium">${retail.name}</td>`;
                        html += `<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">${retail.distributor?.name || '-'}</td>`;
                        html += `<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">${retail.city || '-'}</td>`;
                        html += `<td class="px-4 py-3 text-right"><span class="px-3 py-1 text-xs font-bold rounded-full ${retail.stock_count < 10 ? 'bg-orange-100 text-orange-800' : 'bg-purple-100 text-purple-800'}">${retail.stock_count || 0} units</span></td>`;
                        html += '</tr>';
                    });
                    
                    html += '</tbody></table>';
                    document.getElementById('retailTableContent').innerHTML = html;
                });
        }

        function closeRetailModal() {
            document.getElementById('retailModal').classList.add('hidden');
        }
    </script>
</x-app-layout>

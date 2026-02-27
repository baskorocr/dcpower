<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klaim Garansi - {{ $retail->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">{{ $retail->name }}</h1>
                    <p class="text-sm text-gray-600">Klaim Garansi</p>
                </div>
                <a href="{{ route('warranty.replacement.logout') }}" class="text-red-600 hover:text-red-700">Logout</a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Daftar Klaim yang Disetujui</h2>

            @if($claims->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Claim Number</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Serial Number</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Complaint</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($claims as $claim)
                        <tr>
                            <td class="px-4 py-3 font-mono text-sm">{{ $claim->claim_number }}</td>
                            <td class="px-4 py-3 font-mono">{{ $claim->product->serial_number }}</td>
                            <td class="px-4 py-3 text-sm">{{ ucfirst($claim->complaint_type) }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ ucfirst($claim->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if(!$claim->replacement_product_id)
                                <a href="{{ route('warranty.replacement.show', $claim) }}" class="inline-block px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                                    Scan Replacement
                                </a>
                                @else
                                <span class="text-green-600 font-semibold">✓ Completed</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-600">Tidak ada klaim yang perlu diproses</p>
            </div>
            @endif
        </div>
    </div>
</body>
</html>

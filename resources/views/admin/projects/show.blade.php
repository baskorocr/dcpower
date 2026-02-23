<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $project->name }}</h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Project QR Code -->
        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl border-2 border-blue-100 dark:border-blue-800">
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Project QR Code</h3>
            <div class="flex items-center gap-6">
                @if($project->qr_code)
                <div class="bg-white p-4 rounded-lg">
                    {!! QrCode::size(200)->generate($project->qr_code) !!}
                </div>
                @else
                <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-lg">
                    <p class="text-gray-500 dark:text-gray-400">QR Code not generated</p>
                </div>
                @endif
                <div class="flex-1">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                        <strong>Project Code:</strong> {{ $project->code ?? 'N/A' }}
                    </p>
                    @if($project->qr_code)
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Scan this QR code when creating products for this project
                    </p>
                    <button onclick="printQR()" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        Print QR Code
                    </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl border-2 border-emerald-100 dark:border-emerald-800">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                    <p class="font-semibold">{{ ucfirst($project->status) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Warranty Duration</p>
                    <p class="font-semibold">{{ $project->warranty_duration }} months</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Created By</p>
                    <p class="font-semibold">{{ $project->creator->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Created At</p>
                    <p class="font-semibold">{{ $project->created_at->format('d M Y') }}</p>
                </div>
            </div>
            @if($project->description)
            <div class="mt-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Description</p>
                <p class="mt-1">{{ $project->description }}</p>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function printQR() {
            const printWindow = window.open('', '', 'width=600,height=600');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Print QR Code - {{ $project->name }}</title>
                    <style>
                        body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }
                        h2 { margin-bottom: 10px; }
                        .qr-container { margin: 20px auto; }
                    </style>
                </head>
                <body>
                    <h2>{{ $project->name }}</h2>
                    <p>Project Code: {{ $project->code }}</p>
                    <div class="qr-container">
                        {!! QrCode::size(300)->generate($project->qr_code) !!}
                    </div>
                    <p>Scan to create products for this project</p>
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }
    </script>
    @endpush
</x-app-layout>

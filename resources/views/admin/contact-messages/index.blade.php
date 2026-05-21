<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Contact Messages</h2>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
        <div class="p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
        @endif

        <!-- Filters -->
        <div class="p-4 bg-white dark:bg-dark-eval-1 rounded-2xl">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <select name="status" class="px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg dark:bg-dark-eval-2 text-sm">
                    <option value="">All Status</option>
                    <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>NEW</option>
                    <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read</option>
                    <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>Replied</option>
                </select>

                <input type="date" name="start_date" value="{{ request('start_date') }}" placeholder="Start Date" class="px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg dark:bg-dark-eval-2 text-sm">
                
                <input type="date" name="end_date" value="{{ request('end_date') }}" placeholder="End Date" class="px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg dark:bg-dark-eval-2 text-sm">
                
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">Filter</button>
                    <a href="{{ route('contact-messages.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 text-sm">Reset</a>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-dark-eval-1 rounded-2xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Message</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($messages as $message)
                        <tr class="{{ $message->status === 'new' ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $message->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $message->email }}</td>
                            <td class="px-6 py-4">{{ Str::limit($message->message, 50) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $message->created_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($message->status === 'new')
                                <span class="px-2 py-1 text-xs bg-blue-200 text-blue-800 rounded">NEW</span>
                                @elseif($message->status === 'read')
                                <span class="px-2 py-1 text-xs bg-gray-200 text-gray-800 rounded">Read</span>
                                @else
                                <span class="px-2 py-1 text-xs bg-green-200 text-green-800 rounded">Replied</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('contact-messages.show', $message) }}" class="text-blue-600 hover:text-blue-800">View</a>
                                    
                                    <button onclick="openStatusModal({{ $message->id }}, '{{ $message->status }}')" class="text-purple-600 hover:text-purple-800">
                                        Status
                                    </button>

                                    <form action="{{ route('contact-messages.destroy', $message) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Delete this message?')" class="text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No messages yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $messages->links() }}
        </div>
    </div>

    <!-- Status Modal -->
    <div id="statusModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-dark-eval-1 rounded-lg p-6 w-96">
            <h3 class="text-lg font-bold mb-4">Update Status</h3>
            
            <form id="statusForm" method="POST">
                @csrf
                <div class="space-y-3">
                    <label class="flex items-center p-3 border rounded cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800">
                        <input type="radio" name="status" value="new" class="mr-3">
                        <span class="px-2 py-1 text-xs bg-blue-200 text-blue-800 rounded">NEW</span>
                    </label>
                    
                    <label class="flex items-center p-3 border rounded cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800">
                        <input type="radio" name="status" value="read" class="mr-3">
                        <span class="px-2 py-1 text-xs bg-gray-200 text-gray-800 rounded">Read</span>
                    </label>
                    
                    <label class="flex items-center p-3 border rounded cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800">
                        <input type="radio" name="status" value="replied" class="mr-3">
                        <span class="px-2 py-1 text-xs bg-green-200 text-green-800 rounded">Replied</span>
                    </label>
                </div>

                <div class="flex gap-2 mt-6">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Update
                    </button>
                    <button type="button" onclick="closeStatusModal()" class="flex-1 px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openStatusModal(messageId, currentStatus) {
            const modal = document.getElementById('statusModal');
            const form = document.getElementById('statusForm');
            
            form.action = `/contact-messages/${messageId}/status`;
            
            // Set current status
            document.querySelectorAll('input[name="status"]').forEach(radio => {
                radio.checked = radio.value === currentStatus;
            });
            
            modal.classList.remove('hidden');
        }

        function closeStatusModal() {
            document.getElementById('statusModal').classList.add('hidden');
        }

        // Close modal on outside click
        document.getElementById('statusModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeStatusModal();
            }
        });
    </script>
</x-app-layout>

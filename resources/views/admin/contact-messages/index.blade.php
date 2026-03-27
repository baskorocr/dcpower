<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Contact Messages</h2>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
        <div class="p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
        @endif

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
                        <tr class="{{ !$message->is_read ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $message->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $message->email }}</td>
                            <td class="px-6 py-4">{{ Str::limit($message->message, 50) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $message->created_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($message->is_read)
                                <span class="px-2 py-1 text-xs bg-gray-200 text-gray-800 rounded">Read</span>
                                @else
                                <span class="px-2 py-1 text-xs bg-blue-200 text-blue-800 rounded">New</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('contact-messages.show', $message) }}" class="text-blue-600 hover:text-blue-800 mr-3">View</a>
                                <form action="{{ route('contact-messages.destroy', $message) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Delete this message?')" class="text-red-600 hover:text-red-800">Delete</button>
                                </form>
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
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Contact Message Details</h2>
            <a href="{{ route('contact-messages.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">Back</a>
        </div>
    </x-slot>

    <div class="bg-white dark:bg-dark-eval-1 rounded-2xl shadow-lg p-6">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                <p class="text-lg">{{ $message->name }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                <p class="text-lg">
                    <a href="mailto:{{ $message->email }}" class="text-blue-600 hover:text-blue-800">{{ $message->email }}</a>
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                <p class="text-lg">{{ $message->created_at->format('d M Y H:i') }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Message</label>
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <p class="whitespace-pre-wrap">{{ $message->message }}</p>
                </div>
            </div>

            <div class="pt-4 border-t">
                <form action="{{ route('contact-messages.destroy', $message) }}" method="POST" onsubmit="return confirm('Delete this message?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete Message</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

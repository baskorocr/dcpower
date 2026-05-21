<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Product Audit Logs</h2>
    </x-slot>

    <div class="space-y-4">
        <!-- Filters -->
        <div class="p-4 bg-white dark:bg-dark-eval-1 rounded-2xl">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search serial number..." class="px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg dark:bg-dark-eval-2 text-sm">
                
                <select name="action" class="px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg dark:bg-dark-eval-2 text-sm">
                    <option value="">All Actions</option>
                    <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                    <option value="serial_switched" {{ request('action') == 'serial_switched' ? 'selected' : '' }}>Serial Switched</option>
                </select>

                <select name="user_id" class="px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg dark:bg-dark-eval-2 text-sm">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
                
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">Filter</button>
                    <a href="{{ route('product-audit-logs.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 text-sm">Reset</a>
                </div>
            </form>
        </div>

        <!-- Logs Table -->
        <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Time</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">User</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Action</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Product</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Changes</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-3 text-sm">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                            <td class="px-4 py-3 text-sm">{{ $log->user->name }}</td>
                            <td class="px-4 py-3">
                                @if($log->action == 'deleted')
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Deleted</span>
                                @elseif($log->action == 'serial_switched')
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">Serial Switched</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($log->product)
                                    <div class="font-mono">{{ $log->product->serial_number }}</div>
                                    <div class="text-xs text-gray-500">{{ $log->product->project->name ?? 'N/A' }}</div>
                                @else
                                    <span class="text-gray-400">Deleted</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($log->action == 'serial_switched')
                                    <div class="text-xs">
                                        <span class="text-red-600">{{ $log->old_values['serial_number'] ?? '' }}</span>
                                        →
                                        <span class="text-green-600">{{ $log->new_values['serial_number'] ?? '' }}</span>
                                    </div>
                                @elseif($log->action == 'deleted')
                                    <div class="text-xs text-gray-500">
                                        SN: {{ $log->old_values['serial_number'] ?? 'N/A' }}<br>
                                        Status: {{ $log->old_values['status'] ?? 'N/A' }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $log->ip_address }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">No audit logs found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

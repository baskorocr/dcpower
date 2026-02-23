<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Users Management</h2>
            <a href="{{ route('users.create') }}" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-lg hover:scale-105 transition-transform">
                + New User
            </a>
        </div>
    </x-slot>

    <div class="p-6 bg-white dark:bg-dark-eval-1 rounded-2xl border-2 border-emerald-100 dark:border-emerald-800">
        @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-50 dark:bg-emerald-900/20 border-l-4 border-emerald-500 text-emerald-700 dark:text-emerald-400">
            {{ session('success') }}
        </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-emerald-50 dark:bg-emerald-900/20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase">Roles</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase">Projects</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($users as $user)
                    <tr class="hover:bg-emerald-50 dark:hover:bg-emerald-900/10">
                        <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            @foreach($user->roles as $role)
                            <span class="px-2 py-1 text-xs rounded-full bg-emerald-100 text-emerald-700 mr-1">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td class="px-4 py-3">
                            @foreach($user->allProjects() as $project)
                            <span class="px-2 py-1 text-xs rounded-full bg-cyan-100 text-cyan-700 mr-1">{{ $project->name }}</span>
                            @endforeach
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('users.edit', $user) }}" class="text-emerald-600 hover:text-emerald-700">Edit</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>

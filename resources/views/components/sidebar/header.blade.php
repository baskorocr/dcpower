<div class="flex items-center justify-between px-4 py-4">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
        <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-lg flex items-center justify-center">
            <x-application-logo aria-hidden="true" class="w-5 h-5 text-white" />
        </div>
        <span class="text-lg font-bold text-gray-900 dark:text-white" x-show="isSidebarOpen || isSidebarHovered" x-transition>DC Power</span>
    </a>
    <x-button type="button" icon-only sr-text="Toggle" variant="secondary" x-show="isSidebarOpen || isSidebarHovered" x-on:click="isSidebarOpen = !isSidebarOpen">
        <x-heroicon-o-x class="w-5 h-5" />
    </x-button>
</div>

<nav aria-label="secondary" x-data="{ open: false }" class="sticky top-0 z-10 flex items-center justify-between px-4 py-3 sm:px-6 transition-transform duration-500 bg-white/90 backdrop-blur-xl border-b-2 border-emerald-100 dark:bg-dark-eval-1/90 dark:border-emerald-800" :class="{ '-translate-y-full': scrollingDown, 'translate-y-0': scrollingUp }">
    <div class="flex items-center gap-3">
        <x-button type="button" class="md:hidden" icon-only variant="secondary" sr-text="Toggle dark mode" x-on:click="toggleTheme">
            <x-heroicon-o-moon x-show="!isDarkMode" aria-hidden="true" class="w-5 h-5" />
            <x-heroicon-o-sun x-show="isDarkMode" aria-hidden="true" class="w-5 h-5" />
        </x-button>
    </div>

    <div class="flex items-center gap-3">
        <x-button type="button" class="hidden md:inline-flex hover:bg-emerald-50 dark:hover:bg-emerald-900/20" icon-only variant="secondary" sr-text="Toggle dark mode" x-on:click="toggleTheme">
            <x-heroicon-o-moon x-show="!isDarkMode" aria-hidden="true" class="w-5 h-5" />
            <x-heroicon-o-sun x-show="isDarkMode" aria-hidden="true" class="w-5 h-5" />
        </x-button>

        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="flex items-center gap-2 px-3 py-2 text-sm font-semibold text-gray-700 rounded-xl transition duration-150 ease-in-out hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:text-gray-300 dark:hover:bg-emerald-900/20">
                    <div class="w-9 h-9 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center text-white font-bold text-sm shadow-lg">
                        {{ substr(Auth::user()->name, 0, 2) }}
                    </div>
                    <div class="hidden md:block">{{ Auth::user()->name }}</div>
                    <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </x-slot>

            <x-slot name="content">
                <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</nav>

<div class="fixed inset-x-0 bottom-0 z-50 flex items-center justify-between px-4 py-3 transition-transform duration-500 bg-white/90 backdrop-blur-xl border-t-2 border-emerald-100 md:hidden dark:bg-dark-eval-1/90 dark:border-emerald-800" :class="{ 'translate-y-full': scrollingDown, 'translate-y-0': scrollingUp }">
    <x-button type="button" icon-only variant="secondary" sr-text="Search">
        <x-heroicon-o-search aria-hidden="true" class="w-5 h-5" />
    </x-button>

    <a href="{{ route('dashboard') }}">
        <div class="p-2.5 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-2xl shadow-lg">
            <x-application-logo aria-hidden="true" class="w-6 h-6 text-white" />
        </div>
        <span class="sr-only">Dashboard</span>
    </a>

    <x-button type="button" icon-only variant="secondary" sr-text="Open main menu" x-on:click="isSidebarOpen = !isSidebarOpen">
        <x-heroicon-o-menu x-show="!isSidebarOpen" aria-hidden="true" class="w-5 h-5" />
        <x-heroicon-o-x x-show="isSidebarOpen" aria-hidden="true" class="w-5 h-5" />
    </x-button>
</div>

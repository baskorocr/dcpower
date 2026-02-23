<main class="flex flex-col items-center justify-center flex-1 px-4 py-6 min-h-screen">
    <div class="mb-6 animate-bounce">
        <a href="/">
            <x-application-logo class="w-20 h-20" />
        </a>
    </div>

    <div class="w-full px-8 py-8 overflow-hidden bg-white rounded-2xl shadow-2xl sm:max-w-md dark:bg-dark-eval-1 border-t-4 border-green-600 transform transition-all duration-300 hover:shadow-green-200 dark:hover:shadow-green-900">
        {{ $slot }}
    </div>
</main>

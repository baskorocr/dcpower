<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('dc.png') }}">

    <!-- Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet" />

    <!-- Styles -->
    <style>
        [x-cloak] {
            display: none;
        }
        
        .bg-charging {
            background-image: url('/Untitled.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div
        x-data="mainState"
        class="font-sans antialiased"
        :class="{dark: isDarkMode}"
        x-cloak
    >
        <div class="flex flex-col min-h-screen text-gray-900 bg-charging dark:text-gray-200 relative overflow-hidden">
            <!-- Overlay gradasi hijau transparan -->
            <div class="absolute inset-0 bg-gradient-to-br from-green-500/20 via-emerald-500/10 to-teal-500/20 dark:from-green-900/30 dark:via-emerald-900/20 dark:to-teal-900/30"></div>
            
            <div class="relative z-10">
                {{ $slot }}
            </div>

            <x-footer />
        </div>

        <div class="fixed top-10 right-10 z-50">
            <x-button
                type="button"
                icon-only
                variant="secondary"
                sr-text="Toggle dark mode"
                x-on:click="toggleTheme"
                class="shadow-lg hover:shadow-xl transition-shadow duration-300"
            >
                <x-heroicon-o-moon
                    x-show="!isDarkMode"
                    aria-hidden="true"
                    class="w-6 h-6"
                />

                <x-heroicon-o-sun
                    x-show="isDarkMode"
                    aria-hidden="true"
                    class="w-6 h-6"
                />
            </x-button>
        </div>
    </div>
</body>
</html>

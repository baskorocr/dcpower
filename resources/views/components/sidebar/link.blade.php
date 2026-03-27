@props(['isActive' => false, 'title' => '', 'collapsible' => false])

@php
    $isActiveClasses = $isActive 
        ? 'text-emerald-600 bg-emerald-50 dark:text-emerald-400 dark:bg-emerald-900/30' 
        : 'text-gray-600 hover:text-emerald-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-emerald-400 dark:hover:bg-dark-eval-2';
    $classes = 'flex items-center gap-3 px-4 py-2.5 transition-colors ' . $isActiveClasses;
    if($collapsible) $classes .= ' w-full';
@endphp

@if ($collapsible)
    <button type="button" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon ?? false) {{ $icon }} @else <x-icons.empty-circle class="w-5 h-5" /> @endif
        <span class="text-sm font-medium" x-show="isSidebarOpen || isSidebarHovered">{{ $title }}</span>
        @if ($badge ?? false)
            <span x-show="isSidebarOpen || isSidebarHovered">{{ $badge }}</span>
        @endif
    </button>
@else
    <a {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon ?? false) {{ $icon }} @else <x-icons.empty-circle class="w-5 h-5" /> @endif
        <span class="text-sm font-medium" x-show="isSidebarOpen || isSidebarHovered">{{ $title }}</span>
        @if ($badge ?? false)
            <span x-show="isSidebarOpen || isSidebarHovered">{{ $badge }}</span>
        @endif
    </a>
@endif

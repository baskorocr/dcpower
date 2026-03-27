<x-perfect-scrollbar as="nav" aria-label="main" class="flex flex-col flex-1 gap-1 px-3 py-4">
    
    <x-sidebar.link title="Dashboard" href="{{ route('dashboard') }}" :isActive="request()->routeIs('dashboard')">
        <x-slot name="icon">
            <x-icons.dashboard class="w-5 h-5" />
        </x-slot>
    </x-sidebar.link>

    @can('manage-users')
    <x-sidebar.link title="Users" href="{{ route('users.index') }}" :isActive="request()->routeIs('users.*')">
        <x-slot name="icon">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </x-slot>
    </x-sidebar.link>
    @endcan

    @can('manage-roles')
    <x-sidebar.link title="Roles & Permissions" href="{{ route('roles.index') }}" :isActive="request()->routeIs('roles.*')">
        <x-slot name="icon">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </x-slot>
    </x-sidebar.link>
    @endcan

    @can('manage-projects')
    <x-sidebar.link title="Projects" href="{{ route('projects.index') }}" :isActive="request()->routeIs('projects.*')">
        <x-slot name="icon">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
        </x-slot>
    </x-sidebar.link>
    @endcan

    @can('manage-distributors')
    <x-sidebar.link title="Distributors" href="{{ route('distributors.index') }}" :isActive="request()->routeIs('distributors.*')">
        <x-slot name="icon">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
        </x-slot>
    </x-sidebar.link>
    @endcan

    @can('manage-retails')
    <x-sidebar.link title="Retails" href="{{ route('retails.index') }}" :isActive="request()->routeIs('retails.*')">
        <x-slot name="icon">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </x-slot>
    </x-sidebar.link>
    @endcan

    @can('manage-products')
    <x-sidebar.link title="Products" href="{{ route('products.index') }}" :isActive="request()->routeIs('products.*')">
        <x-slot name="icon">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
        </x-slot>
    </x-sidebar.link>
    @endcan

    @can('scan-qr')
    <x-sidebar.link title="QR Scan" href="{{ route('qr-scan.index') }}" :isActive="request()->routeIs('qr-scan.*')">
        <x-slot name="icon">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
            </svg>
        </x-slot>
    </x-sidebar.link>
    @endcan

    @can('stock-out')
    <x-sidebar.link title="Stock Out" href="{{ route('stock-out.index') }}" :isActive="request()->routeIs('stock-out.*')">
        <x-slot name="icon">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
        </x-slot>
    </x-sidebar.link>
    @endcan

    @canany(['view-claims', 'manage-claims'])
    <x-sidebar.link title="Warranty Claims" href="{{ route('warranty-claims.index') }}" :isActive="request()->routeIs('warranty-claims.*')">
        <x-slot name="icon">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </x-slot>
    </x-sidebar.link>
    @endcanany

    @can('approve-claims')
    <x-sidebar.link title="Claim Approvals" href="{{ route('claim-approvals.index') }}" :isActive="request()->routeIs('claim-approvals.*')">
        <x-slot name="icon">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
        </x-slot>
    </x-sidebar.link>
    @endcan

    @can('view-claim-history')
    <x-sidebar.link title="Claim History" href="{{ route('claim-history.index') }}" :isActive="request()->routeIs('claim-history.*')">
        <x-slot name="icon">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </x-slot>
    </x-sidebar.link>
    @endcan

    @can('manage-contact-messages')
    <x-sidebar.link title="Contact Messages" href="{{ route('contact-messages.index') }}" :isActive="request()->routeIs('contact-messages.*')">
        <x-slot name="icon">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </x-slot>
        @if(isset($unreadMessages) && $unreadMessages > 0)
        <x-slot name="badge">
            <span class="ml-auto px-2 py-0.5 text-xs bg-red-500 text-white rounded-full">{{ $unreadMessages }}</span>
        </x-slot>
        @endif
    </x-sidebar.link>
    @endcan

</x-perfect-scrollbar>

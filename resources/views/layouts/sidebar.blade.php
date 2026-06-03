@php
    $isSuperAdmin = auth()->user()->isSuperAdmin();
    $isCompanyAdmin = auth()->user()->isAdmin() && auth()->user()->company_id !== null;
    $isCompanyMember = auth()->user()->isMember() && auth()->user()->company_id !== null;
    $homeRoute = auth()->user()->homeRoute();
@endphp

{{-- Mobile backdrop --}}
<div
    x-show="sidebarOpen"
    x-transition:enter="transition-opacity ease-linear duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden"
    @click="sidebarOpen = false"
    x-cloak
></div>

<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-gray-200 bg-white transition-transform duration-200 ease-in-out lg:static lg:z-auto lg:shrink-0 lg:translate-x-0"
>
    {{-- Brand --}}
    <div class="flex h-16 shrink-0 items-center gap-2 border-b border-gray-200 px-4">
        <a href="{{ $homeRoute }}" class="flex min-w-0 items-center gap-2" @click="sidebarOpen = false">
            <x-application-logo class="block h-8 w-auto shrink-0 fill-current text-gray-800" />
            <div class="min-w-0">
                <span class="block truncate text-sm font-semibold text-gray-900">{{ config('app.name') }}</span>
                <span class="block truncate text-xs font-medium text-indigo-600">{{ auth()->user()->role->label() }} {{ __('Panel') }}</span>
            </div>
        </a>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        @if ($isSuperAdmin)
            <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">
                {{ __('Super Admin') }}
            </p>
            <x-sidebar-link
                :href="route('super-admin.dashboard')"
                :active="request()->routeIs('super-admin.dashboard')"
            >
                <x-slot name="icon">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                </x-slot>
                {{ __('Dashboard') }}
            </x-sidebar-link>

            <p class="mb-2 mt-6 px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">
                {{ __('Management') }}
            </p>
            <x-sidebar-link
                :href="route('super-admin.companies.index')"
                :active="request()->routeIs('super-admin.companies.*')"
            >
                <x-slot name="icon">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5M2.25 9h19.5M2.25 4.5h19.5m-16.5 0V9m0 6v7.5m6-13.5V9m0 6v7.5m6-13.5V9m0 6v7.5" />
                    </svg>
                </x-slot>
                {{ __('Companies') }}
            </x-sidebar-link>
            <x-sidebar-link
                :href="route('super-admin.short-urls.index')"
                :active="request()->routeIs('super-admin.short-urls.*')"
            >
                <x-slot name="icon">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                    </svg>
                </x-slot>
                {{ __('Short URLs') }}
            </x-sidebar-link>
            <x-sidebar-link
                :href="route('super-admin.admins.index')"
                :active="request()->routeIs('super-admin.admins.*')"
            >
                <x-slot name="icon">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </x-slot>
                {{ __('Admins') }}
            </x-sidebar-link>
            <x-sidebar-link
                :href="route('super-admin.members.index')"
                :active="request()->routeIs('super-admin.members.*')"
            >
                <x-slot name="icon">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </x-slot>
                {{ __('Members') }}
            </x-sidebar-link>
        @else
            <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">
                {{ __('Menu') }}
            </p>
            <x-sidebar-link
                :href="route('dashboard')"
                :active="request()->routeIs('dashboard')"
            >
                <x-slot name="icon">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                </x-slot>
                {{ __('Dashboard') }}
            </x-sidebar-link>

            @if ($isCompanyAdmin)
                <x-sidebar-link
                    :href="route('admin.team.index')"
                    :active="request()->routeIs('admin.team.*')"
                >
                    <x-slot name="icon">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </x-slot>
                    {{ __('Team') }}
                </x-sidebar-link>
                <x-sidebar-link
                    :href="route('admin.short-urls.index')"
                    :active="request()->routeIs('admin.short-urls.*')"
                >
                    <x-slot name="icon">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                        </svg>
                    </x-slot>
                    {{ __('Short URLs') }}
                </x-sidebar-link>
            @elseif ($isCompanyMember)
                <x-sidebar-link
                    :href="route('member.short-urls.index')"
                    :active="request()->routeIs('member.short-urls.*')"
                >
                    <x-slot name="icon">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                        </svg>
                    </x-slot>
                    {{ __('Short URLs') }}
                </x-sidebar-link>
            @endif
        @endif

        <p class="mb-2 mt-6 px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">
            {{ __('Account') }}
        </p>
        <x-sidebar-link
            :href="route('profile.edit')"
            :active="request()->routeIs('profile.edit')"
        >
            <x-slot name="icon">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
            </x-slot>
            {{ __('Profile') }}
        </x-sidebar-link>
    </nav>

    {{-- User footer --}}
    <div class="shrink-0 border-t border-gray-200 p-4">
        <div class="mb-3 truncate">
            <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
            <p class="truncate text-xs text-gray-500">{{ Auth::user()->email }}</p>
            <p class="mt-0.5 text-xs capitalize text-gray-400">{{ str_replace('_', ' ', auth()->user()->role->value) }}</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="flex w-full items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                </svg>
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</aside>

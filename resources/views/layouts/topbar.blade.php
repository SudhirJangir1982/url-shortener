<header class="sticky top-0 z-30 flex h-16 shrink-0 items-center gap-4 border-b border-gray-200 bg-white px-4 sm:px-6 lg:px-8">
    <button
        type="button"
        @click="sidebarOpen = true"
        class="inline-flex items-center justify-center rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 lg:hidden"
        aria-label="{{ __('Open sidebar') }}"
    >
        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <div class="flex min-w-0 flex-1 items-center overflow-hidden">
        @isset($header)
            <div class="min-w-0">
                {{ $header }}
            </div>
        @endisset
    </div>
</header>

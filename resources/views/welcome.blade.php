<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — Sign in</title>
            @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
<body class="min-h-screen bg-slate-950 font-sans antialiased">
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="absolute -left-32 top-0 h-96 w-96 rounded-full bg-amber-500/20 blur-3xl"></div>
        <div class="absolute right-0 top-1/4 h-80 w-80 rounded-full bg-blue-500/20 blur-3xl"></div>
        <div class="absolute bottom-0 left-1/3 h-72 w-72 rounded-full bg-emerald-500/15 blur-3xl"></div>
    </div>

    <div class="relative flex min-h-screen flex-col items-center justify-center px-4 py-12">
        <div class="mb-10 w-full max-w-5xl text-center">
            <x-application-logo class="mx-auto h-10 w-auto fill-current text-white" />
            <h1 class="mt-5 text-2xl font-bold text-white sm:text-3xl">Sembark URL Shortener</h1>
            <p class="mt-2 text-sm text-slate-400">Who are you? Choose a role to sign in.</p>
        </div>

        {{-- Three boxes in one row: [ ] [ ] [ ] --}}
        <div class="flex w-full max-w-5xl flex-row items-stretch justify-center gap-3 sm:gap-5">
            @foreach (\App\Enums\UserRole::loginOptions() as $role)
                @php
                    $theme = match ($role) {
                        \App\Enums\UserRole::SuperAdmin => [
                            'border' => 'border-amber-500/40 hover:border-amber-400',
                            'ring' => 'hover:ring-amber-500/30',
                            'icon_bg' => 'bg-amber-500/20 text-amber-400',
                            'accent' => 'text-amber-400',
                            'bar' => 'bg-amber-500',
                        ],
                        \App\Enums\UserRole::Admin => [
                            'border' => 'border-blue-500/40 hover:border-blue-400',
                            'ring' => 'hover:ring-blue-500/30',
                            'icon_bg' => 'bg-blue-500/20 text-blue-400',
                            'accent' => 'text-blue-400',
                            'bar' => 'bg-blue-500',
                        ],
                        \App\Enums\UserRole::Member => [
                            'border' => 'border-emerald-500/40 hover:border-emerald-400',
                            'ring' => 'hover:ring-emerald-500/30',
                            'icon_bg' => 'bg-emerald-500/20 text-emerald-400',
                            'accent' => 'text-emerald-400',
                            'bar' => 'bg-emerald-500',
                        ],
                    };
                @endphp

                <a
                    href="{{ route('login.role', $role->slug()) }}"
                    class="group flex min-h-[200px] min-w-0 flex-1 flex-col rounded-xl border-2 bg-slate-900/90 p-4 text-center shadow-lg ring-0 transition hover:-translate-y-0.5 hover:shadow-xl hover:ring-2 sm:min-h-[240px] sm:p-6 {{ $theme['border'] }} {{ $theme['ring'] }}"
                >
                    <div class="mx-auto h-1 w-12 rounded-full {{ $theme['bar'] }}"></div>

                    <div class="mx-auto mt-4 flex h-11 w-11 items-center justify-center rounded-lg {{ $theme['icon_bg'] }} sm:h-12 sm:w-12">
                        @if ($role === \App\Enums\UserRole::SuperAdmin)
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                        @elseif ($role === \App\Enums\UserRole::Admin)
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    @else
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                        @endif
                    </div>

                    <h2 class="mt-4 text-base font-semibold text-white sm:text-lg">{{ $role->label() }}</h2>

                    <p class="mt-2 flex-1 text-xs leading-snug text-slate-400 sm:text-sm">
                        {{ $role->description() }}
                    </p>

                    <span class="mt-4 text-xs font-semibold uppercase tracking-wide {{ $theme['accent'] }} sm:text-sm">
                        Sign in →
                    </span>
                </a>
            @endforeach
        </div>
    </div>
    </body>
</html>

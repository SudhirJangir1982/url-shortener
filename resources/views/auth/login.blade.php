<x-guest-layout>
    <div class="mb-6">
        <a href="{{ url('/') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to role selection</a>

        @php
            $badgeStyles = match ($loginRole) {
                \App\Enums\UserRole::SuperAdmin => 'bg-amber-100 text-amber-800',
                \App\Enums\UserRole::Admin => 'bg-blue-100 text-blue-800',
                \App\Enums\UserRole::Member => 'bg-emerald-100 text-emerald-800',
            };
        @endphp

        <span class="mt-3 inline-block rounded-full px-3 py-1 text-xs font-semibold {{ $badgeStyles }}">
            {{ $loginRole->label() }} login
        </span>

        <h2 class="mt-3 text-xl font-semibold text-gray-900">{{ __('Sign in') }}</h2>
        <p class="mt-1 text-sm text-gray-600">{{ $loginRole->description() }}</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login.role.store', $loginRole->slug()) }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex flex-col gap-3 mt-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-col gap-1 text-sm">
                @if ($loginRole->canRegister())
                    <a class="text-gray-600 hover:text-gray-900 underline" href="{{ route('register.role', $loginRole->slug()) }}">
                        {{ __('Need an account? Register') }}
                    </a>
                @endif
                @if (Route::has('password.request'))
                    <a class="text-gray-600 hover:text-gray-900 underline" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <x-primary-button class="justify-center sm:ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

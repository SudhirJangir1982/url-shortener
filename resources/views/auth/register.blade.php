<x-guest-layout>
    <div class="mb-6">
        <a href="{{ url('/') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to role selection</a>

        @php
            $badgeStyles = match ($registerRole) {
                \App\Enums\UserRole::Admin => 'bg-blue-100 text-blue-800',
                \App\Enums\UserRole::Member => 'bg-emerald-100 text-emerald-800',
            };
        @endphp

        <span class="mt-3 inline-block rounded-full px-3 py-1 text-xs font-semibold {{ $badgeStyles }}">
            {{ $registerRole->label() }} registration
        </span>

        <h2 class="mt-3 text-xl font-semibold text-gray-900">{{ __('Create account') }}</h2>
        <p class="mt-1 text-sm text-gray-600">{{ $registerRole->description() }}</p>
    </div>

    <form method="POST" action="{{ route('register.role.store', $registerRole->slug()) }}">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        @if ($registerRole === \App\Enums\UserRole::Admin)
            <div class="mt-4">
                <x-input-label for="company_name" :value="__('Company name')" />
                <x-text-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name')" required />
                <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="company_email" :value="__('Company email')" />
                <x-text-input id="company_email" class="block mt-1 w-full" type="email" name="company_email" :value="old('company_email')" required />
                <x-input-error :messages="$errors->get('company_email')" class="mt-2" />
            </div>
        @endif

        @if ($registerRole === \App\Enums\UserRole::Member)
            <div class="mt-4">
                <x-input-label for="company_id" :value="__('Company')" />
                <select
                    id="company_id"
                    name="company_id"
                    class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required
                >
                    <option value="">{{ __('Select your company') }}</option>
                    @forelse ($companies as $company)
                        <option value="{{ $company->id }}" @selected(old('company_id') == $company->id)>
                            {{ $company->name }}
                        </option>
                    @empty
                        <option value="" disabled>{{ __('No companies available — ask an admin to register first') }}</option>
                    @endforelse
                </select>
                <x-input-error :messages="$errors->get('company_id')" class="mt-2" />
            </div>
        @endif

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login.role', $registerRole->slug()) }}">
                {{ __('Already registered? Sign in') }}
            </a>

            <x-primary-button>
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

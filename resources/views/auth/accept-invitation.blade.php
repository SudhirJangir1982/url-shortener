<x-guest-layout>
    <div class="mb-4 text-center">
        <h1 class="text-lg font-semibold text-gray-900">{{ __('Accept invitation') }}</h1>
        <p class="mt-2 text-sm text-gray-600">
            {{ __('Join :company as :role', [
                'company' => $invitation->company->name,
                'role' => $invitation->role->label(),
            ]) }}
        </p>
    </div>

    <form method="POST" action="{{ route('invitation.accept.store', $token) }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" class="block mt-1 w-full bg-gray-50" :value="$invitation->email" disabled />
        </div>

        <div>
            <x-input-label for="name" :value="__('Your name')" />
            <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name', $invitation->name)" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" name="password" type="password" class="block mt-1 w-full" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm password')" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="block mt-1 w-full" required autocomplete="new-password" />
        </div>

        <x-primary-button class="w-full justify-center">{{ __('Create account') }}</x-primary-button>
    </form>
</x-guest-layout>

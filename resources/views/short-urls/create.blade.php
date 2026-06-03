<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-800">{{ __('Add short URL') }}</h1>
    </x-slot>

    <div class="max-w-xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                @if ($company)
                    <p class="mb-6 text-sm text-gray-600">
                        {{ __('Creating a link for :company.', ['company' => $company->name]) }}
                    </p>
                @endif

                <form method="POST" action="{{ $storeRoute }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="original_url" :value="__('Destination URL')" />
                        <x-text-input id="original_url" name="original_url" type="text" class="block mt-1 w-full" :value="old('original_url')" required autofocus placeholder="https://example.com/page" inputmode="url" />
                        <p class="mt-1 text-xs text-gray-500">{{ __('Full web address where visitors should land. A random short link is generated for you.') }}</p>
                        <x-input-error :messages="$errors->get('original_url')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="title" :value="__('Title (optional)')" />
                        <x-text-input id="title" name="title" type="text" class="block mt-1 w-full" :value="old('title')" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ $indexRoute }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ __('Cancel') }}
                        </a>
                        <x-primary-button>{{ __('Create short URL') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

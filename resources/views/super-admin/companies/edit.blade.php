<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-800">{{ __('Edit company') }}</h1>
    </x-slot>

    <div class="max-w-xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <form method="POST" action="{{ route('super-admin.companies.update', $company) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" :value="__('Company name')" />
                        <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name', $company->name)" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('Company email')" />
                        <x-text-input id="email" name="email" type="email" class="block mt-1 w-full" :value="old('email', $company->email)" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between gap-3 pt-2">
                        <a href="{{ route('super-admin.companies.show', $company) }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ __('Cancel') }}
                        </a>
                        <x-primary-button>{{ __('Save changes') }}</x-primary-button>
                    </div>
                </form>

                <form method="POST" action="{{ route('super-admin.companies.destroy', $company) }}" class="mt-8 border-t border-gray-200 pt-6" onsubmit="return confirm(@json(__('Delete this company permanently?')))">
                    @csrf
                    @method('DELETE')
                    <p class="mb-3 text-sm text-gray-600">{{ __('Delete only if the company has no users.') }}</p>
                    <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800">
                        {{ __('Delete company') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

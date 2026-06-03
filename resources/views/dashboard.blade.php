<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-800">
            {{ auth()->user()->role->label() }} {{ __('Dashboard') }}
        </h1>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p>{{ __('Welcome, :name.', ['name' => auth()->user()->name]) }}</p>
                    <p class="mt-2 text-sm text-gray-600">{{ auth()->user()->email }}</p>
                    <p class="mt-1 text-sm capitalize text-gray-500">
                        {{ str_replace('_', ' ', auth()->user()->role->value) }}
                    </p>
                    @if (auth()->user()->company)
                        <p class="mt-2 text-sm text-gray-600">
                            {{ __('Company') }}: {{ auth()->user()->company->name }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

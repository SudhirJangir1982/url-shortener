<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-800">{{ $company->name }}</h1>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <x-invitation-link-banner />

    <div class="max-w-7xl mx-auto space-y-6">
        <div class="flex flex-wrap items-center justify-end gap-2">
            <a href="{{ route('super-admin.companies.edit', $company) }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                {{ __('Edit company') }}
            </a>
            <a href="{{ route('super-admin.companies.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                {{ __('Back to list') }}
            </a>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="border-b border-gray-200 px-4 py-4 sm:px-6">
                <h2 class="text-sm font-semibold text-gray-900">{{ __('Company details') }}</h2>
            </div>
            <dl class="grid gap-4 p-4 sm:grid-cols-2 sm:p-6">
                <div>
                    <dt class="text-xs font-medium uppercase text-gray-500">{{ __('Name') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $company->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase text-gray-500">{{ __('Email') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $company->email }}</dd>
                </div>
            </dl>
        </div>

        @include('team.manage', [
            'company' => $company,
            'invitationStoreRoute' => route('super-admin.companies.invitations.store', $company),
            'invitationsDataUrl' => $invitationsDataUrl,
            'membersDataUrl' => $membersDataUrl,
        ])
    </div>
</x-app-layout>

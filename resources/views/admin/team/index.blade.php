<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-800">{{ __('Team') }}</h1>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    <x-invitation-link-banner />

    <div class="max-w-7xl mx-auto space-y-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="border-b border-gray-200 px-4 py-4 sm:px-6">
                <h2 class="text-sm font-semibold text-gray-900">{{ $company->name }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('Manage admins and members in your company.') }}</p>
            </div>
        </div>

        @include('team.manage', [
            'company' => $company,
            'invitationStoreRoute' => route('admin.team.invitations.store'),
            'invitationsDataUrl' => $invitationsDataUrl,
            'membersDataUrl' => $membersDataUrl,
        ])
    </div>
</x-app-layout>

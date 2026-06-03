@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
    <style>
        #team-invitations-datatable_wrapper .dt-layout-row,
        #team-members-datatable_wrapper .dt-layout-row { align-items: center; gap: 0.75rem; }
        #team-invitations-datatable_wrapper .dt-search input,
        #team-invitations-datatable_wrapper .dt-length select,
        #team-members-datatable_wrapper .dt-search input,
        #team-members-datatable_wrapper .dt-length select {
            border-radius: 0.375rem;
            border: 1px solid #d1d5db;
            padding: 0.375rem 0.75rem;
        }
        table.dataTable thead th { border-bottom: 1px solid #e5e7eb !important; }
        table.dataTable tbody td { border-top: 1px solid #f3f4f6 !important; }
        .dataTables_processing {
            background: rgba(255, 255, 255, 0.9) !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 0.5rem !important;
        }
    </style>
@endpush

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="border-b border-gray-200 px-4 py-4 sm:px-6">
        <h2 class="text-sm font-semibold text-gray-900">{{ __('Invite admin or member') }}</h2>
        <p class="mt-1 text-sm text-gray-600">{{ __('Send an invitation link so they can set a password and join this company.') }}</p>
    </div>
    <form method="POST" action="{{ $invitationStoreRoute }}" class="space-y-4 p-4 sm:p-6">
        @csrf

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="name" :value="__('Full name')" />
                <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name')" required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" name="email" type="email" class="block mt-1 w-full" :value="old('email')" required />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
        </div>

        <div class="max-w-xs">
            <x-input-label for="role" :value="__('Role')" />
            <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                <option value="admin" @selected(old('role') === 'admin')>{{ __('Admin') }}</option>
                <option value="member" @selected(old('role', 'member') === 'member')>{{ __('Member') }}</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <div class="pt-2">
            <x-primary-button>{{ __('Create invitation') }}</x-primary-button>
        </div>
    </form>
</div>

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="border-b border-gray-200 px-4 py-4 sm:px-6">
        <h2 class="text-sm font-semibold text-gray-900">{{ __('Pending invitations') }}</h2>
    </div>
    <div class="p-4 sm:p-6">
        <table id="team-invitations-datatable" class="min-w-full display nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('Role') }}</th>
                    <th>{{ __('Expires') }}</th>
                    <th>{{ __('Link') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="border-b border-gray-200 px-4 py-4 sm:px-6">
        <h2 class="text-sm font-semibold text-gray-900">{{ __('Team members') }}</h2>
    </div>
    <div class="p-4 sm:p-6">
        <table id="team-members-datatable" class="min-w-full display nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('Role') }}</th>
                    <th>{{ __('Joined') }}</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dtLanguage = {
                search: @json(__('Search').':'),
                lengthMenu: @json(__('Show').' _MENU_ '.(__('entries'))),
                info: @json(__('Showing').' _START_ '.__('to').' _END_ '.__('of').' _TOTAL_ '.__('entries')),
                infoEmpty: @json(__('Showing 0 entries')),
                zeroRecords: @json(__('No matching records found')),
                processing: @json(__('Loading...')),
                paginate: {
                    first: @json(__('First')),
                    last: @json(__('Last')),
                    next: @json(__('Next')),
                    previous: @json(__('Previous')),
                },
            };

            new DataTable('#team-invitations-datatable', {
                processing: true,
                serverSide: true,
                ajax: { url: @json($invitationsDataUrl), type: 'GET' },
                pageLength: 10,
                lengthMenu: [10, 25, 50],
                order: [[3, 'desc']],
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'role', name: 'role', searchable: false },
                    { data: 'expires_at', name: 'expires_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false },
                ],
                language: dtLanguage,
            });

            new DataTable('#team-members-datatable', {
                processing: true,
                serverSide: true,
                ajax: { url: @json($membersDataUrl), type: 'GET' },
                pageLength: 10,
                lengthMenu: [10, 25, 50],
                order: [[3, 'desc']],
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'role', name: 'role', searchable: false },
                    { data: 'created_at', name: 'created_at' },
                ],
                language: dtLanguage,
            });
        });
    </script>
    @include('components.copy-link-script')
@endpush

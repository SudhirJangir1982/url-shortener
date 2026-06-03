<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-800">{{ $title }}</h1>
    </x-slot>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
        <style>
            #{{ $tableId }}_wrapper .dt-layout-row { align-items: center; gap: 0.75rem; }
            #{{ $tableId }}_wrapper .dt-search input,
            #{{ $tableId }}_wrapper .dt-length select {
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

    <div class="max-w-7xl mx-auto">
        <p class="mb-4 text-sm text-gray-600">
            {{ __('All :role accounts on the platform. Search, sort, and pagination are handled on the server.', ['role' => strtolower($roleLabel).'s']) }}
        </p>

        <div class="overflow-hidden bg-white p-4 shadow-sm sm:rounded-lg sm:p-6">
            <table id="{{ $tableId }}" class="min-w-full display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Role') }}</th>
                        <th>{{ __('Company') }}</th>
                        <th>{{ __('Registered') }}</th>
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
                new DataTable('#{{ $tableId }}', {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: @json($dataUrl),
                        type: 'GET',
                    },
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    order: [[4, 'desc']],
                    columns: [
                        { data: 'name', name: 'name' },
                        { data: 'email', name: 'email' },
                        { data: 'role', name: 'role', orderable: true, searchable: false },
                        { data: 'company_name', name: 'company_name', orderable: true, searchable: true },
                        { data: 'created_at', name: 'created_at' },
                    ],
                    language: {
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
                    },
                });
            });
        </script>
    @endpush
</x-app-layout>

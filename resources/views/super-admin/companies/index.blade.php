<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-800">{{ __('Companies') }}</h1>
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

    @push('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
        <style>
            #companies-datatable_wrapper .dt-layout-row { align-items: center; gap: 0.75rem; }
            #companies-datatable_wrapper .dt-search input,
            #companies-datatable_wrapper .dt-length select {
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
            .companies-card-toolbar {
                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                width: 100%;
            }
            .companies-card-toolbar .toolbar-action {
                flex-shrink: 0;
                margin-left: auto;
            }
            .companies-card-toolbar .companies-add-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
                font-weight: 600;
                line-height: 1.25rem;
                color: #fff;
                background-color: #1f2937;
                border: none;
                border-radius: 0.375rem;
                text-decoration: none;
                white-space: nowrap;
                cursor: pointer;
                transition: background-color 0.15s ease;
            }
            .companies-card-toolbar .companies-add-btn:hover {
                background-color: #374151;
                color: #fff;
            }
        </style>
    @endpush

    <div class="max-w-7xl mx-auto">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="companies-card-toolbar border-b border-gray-200 px-4 py-4 sm:px-6">
                <p class="text-sm text-gray-600 m-0">
                    {{ __('All companies on the platform.') }}
                </p>
                <a
                    href="{{ route('super-admin.companies.create') }}"
                    class="toolbar-action companies-add-btn"
                >
                    {{ __('Add company') }}
                </a>
            </div>

            <div class="p-4 sm:p-6">
                <table id="companies-datatable" class="min-w-full display nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>{{ __('Company') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Users') }}</th>
                            <th>{{ __('Short URLs') }}</th>
                            <th>{{ __('Created') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                new DataTable('#companies-datatable', {
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
                        { data: 'users_count', name: 'users_count', searchable: false },
                        { data: 'short_urls_count', name: 'short_urls_count', orderable: false, searchable: false },
                        { data: 'created_at', name: 'created_at' },
                        { data: 'actions', name: 'actions', orderable: false, searchable: false },
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

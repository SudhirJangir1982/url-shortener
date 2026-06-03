<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-800">{{ __('Short URLs') }}</h1>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    @push('styles')
        <style>
            .short-urls-toolbar {
                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                width: 100%;
            }
            .short-urls-toolbar .toolbar-action {
                flex-shrink: 0;
                margin-left: auto;
            }
            .short-urls-toolbar .short-urls-add-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
                font-weight: 600;
                line-height: 1.25rem;
                color: #fff;
                background-color: #1f2937;
                border-radius: 0.375rem;
                text-decoration: none;
                white-space: nowrap;
            }
            .short-urls-toolbar .short-urls-add-btn:hover {
                background-color: #374151;
                color: #fff;
            }
            .short-urls-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
            .short-urls-table th {
                text-align: left;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                color: #6b7280;
                border-bottom: 1px solid #e5e7eb;
                padding: 0.75rem 0.5rem;
            }
            .short-urls-table td {
                border-top: 1px solid #f3f4f6;
                padding: 0.75rem 0.5rem;
                vertical-align: top;
            }
            .short-urls-table .mono { font-family: ui-monospace, monospace; font-size: 0.8125rem; }
            .short-urls-table a.link { color: #4f46e5; text-decoration: none; }
            .short-urls-table a.link:hover { text-decoration: underline; }
            .short-urls-table .delete-btn {
                font-size: 0.875rem;
                color: #dc2626;
                background: none;
                border: none;
                cursor: pointer;
                padding: 0;
            }
            .short-urls-table .delete-btn:hover { color: #b91c1c; }
        </style>
    @endpush

    <div class="max-w-7xl mx-auto">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="short-urls-toolbar border-b border-gray-200 px-4 py-4 sm:px-6">
                <p class="text-sm text-gray-600 m-0">{{ $listDescription }}</p>
                @if ($canCreate && $createRoute)
                    <a href="{{ $createRoute }}" class="toolbar-action short-urls-add-btn">
                        {{ __('Add short URL') }}
                    </a>
                @endif
            </div>

            <div class="p-4 sm:p-6 overflow-x-auto">
                @if ($shortUrls->isEmpty())
                    <p class="text-sm text-gray-600">
                        @if ($canCreate)
                            {{ __('No short URLs yet. Create your first one.') }}
                        @else
                            {{ __('You have not created any short URLs yet.') }}
                        @endif
                    </p>
                @else
                    <table class="short-urls-table">
                        <thead>
                            <tr>
                                <th>{{ __('Short link') }}</th>
                                <th>{{ __('Destination') }}</th>
                                <th>{{ __('Title') }}</th>
                                @if ($showCreatedBy)
                                    <th>{{ __('Created by') }}</th>
                                @endif
                                <th>{{ __('Created') }}</th>
                                @if ($canDelete)
                                    <th></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($shortUrls as $shortUrl)
                                <tr>
                                    <td class="mono">
                                        <a href="{{ $shortUrl->shortLink() }}" target="_blank" rel="noopener" class="link">
                                            {{ $shortUrl->shortLink() }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ $shortUrl->original_url }}" target="_blank" rel="noopener" class="link break-all">
                                            {{ Str::limit($shortUrl->original_url, 60) }}
                                        </a>
                                    </td>
                                    <td>{{ $shortUrl->title ?? '—' }}</td>
                                    @if ($showCreatedBy)
                                        <td>{{ $shortUrl->user?->name ?? '—' }}</td>
                                    @endif
                                    <td>{{ $shortUrl->created_at->format('M j, Y') }}</td>
                                    @if ($canDelete)
                                        <td>
                                            <form method="POST" action="{{ route($destroyRouteName, $shortUrl) }}" class="inline" onsubmit="return confirm(@json(__('Delete this short URL?')))">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="delete-btn">{{ __('Delete') }}</button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

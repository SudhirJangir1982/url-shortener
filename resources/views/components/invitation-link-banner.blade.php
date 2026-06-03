@if (session('invitation_link'))
    @php($invitationLink = session('invitation_link'))
    <div class="mb-4 rounded-md border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-900">
        <p class="font-medium">{{ __('Invitation link') }}</p>
        <div class="mt-2 flex flex-col gap-2 sm:flex-row sm:items-center">
            <p id="invitation-link-text" class="min-w-0 flex-1 break-all rounded border border-indigo-200 bg-white px-3 py-2 font-mono text-xs text-indigo-950 sm:text-sm">
                {{ $invitationLink }}
            </p>
            <button
                type="button"
                data-copy-url="{{ $invitationLink }}"
                class="copy-invitation-link inline-flex shrink-0 items-center justify-center rounded-md border border-indigo-300 bg-white px-4 py-2 text-sm font-medium text-indigo-800 hover:bg-indigo-100"
            >
                {{ __('Copy link') }}
            </button>
        </div>
        <p class="mt-2 text-xs text-indigo-700">{{ __('Copy this link and send it to the invitee. It expires in 7 days.') }}</p>
    </div>

    @include('components.copy-link-script')
@endif

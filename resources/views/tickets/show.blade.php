<x-app-layout :title="'Ticket #'.$ticket->id">
    <div class="mx-auto max-w-4xl">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <a href="{{ $isAdmin ? route('admin.tickets.index') : route('tickets.index') }}" class="link link-hover text-sm">
                    &larr; {{ $isAdmin ? 'All Tickets' : 'My Tickets' }}
                </a>
                <div class="mt-3 flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl font-bold">Ticket #{{ $ticket->id }}</h1>
                    <x-ticket-status-badge :status="$ticket->status" />
                </div>
            </div>

            @if ($isAdmin && $nextStatus)
                <form action="{{ route('admin.tickets.advance-status', $ticket) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="{{ $nextStatus->value }}" />
                    <button type="submit" class="btn btn-primary">
                        Advance to {{ str($nextStatus->value)->replace('_', ' ')->title() }}
                    </button>
                </form>
            @endif
        </div>

        <x-validation-errors />

        <article class="card border border-base-300 bg-base-100 shadow-sm">
            <div class="card-body gap-6">
                <div>
                    <h2 class="text-xl font-semibold">{{ $ticket->subject }}</h2>
                    <p class="mt-4 whitespace-pre-line leading-7 text-base-content/80">{{ $ticket->description }}</p>
                </div>

                <div class="grid gap-4 border-t border-base-300 pt-5 text-sm sm:grid-cols-2">
                    <div>
                        <p class="text-base-content/60">Created</p>
                        <p class="mt-1 font-medium">{{ $ticket->created_at->format('M j, Y g:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-base-content/60">Last updated</p>
                        <p class="mt-1 font-medium">{{ $ticket->updated_at->format('M j, Y g:i A') }}</p>
                    </div>
                    @if ($isAdmin)
                        <div class="sm:col-span-2">
                            <p class="text-base-content/60">Created by</p>
                            <p class="mt-1 font-medium">{{ $ticket->user->name }} <span class="text-base-content/60">({{ $ticket->user->email }})</span></p>
                        </div>
                    @endif
                </div>
            </div>
        </article>
    </div>
</x-app-layout>

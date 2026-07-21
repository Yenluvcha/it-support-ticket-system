@props(['tickets', 'adminView' => false])

<div class="overflow-x-auto rounded-box border border-base-300 bg-base-100">
    <table class="table table-zebra">
        <thead>
            <tr>
                <th>Ticket</th>
                <th>Subject</th>
                <th>Status</th>
                @if ($adminView)
                    <th>Created by</th>
                @endif
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tickets as $ticket)
                <tr>
                    <td class="font-mono text-xs">#{{ $ticket->id }}</td>
                    <td>
                        <a href="{{ route('tickets.show', $ticket) }}" class="link link-hover font-medium">
                            {{ $ticket->subject }}
                        </a>
                    </td>
                    <td><x-ticket-status-badge :status="$ticket->status" /></td>
                    @if ($adminView)
                        <td>{{ $ticket->user->name }}</td>
                    @endif
                    <td class="whitespace-nowrap text-sm text-base-content/70">{{ $ticket->created_at->diffForHumans() }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $adminView ? 5 : 4 }}" class="py-10 text-center text-base-content/60">
                        No tickets found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

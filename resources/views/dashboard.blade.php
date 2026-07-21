<x-app-layout title="Dashboard">
    <div class="mx-auto max-w-6xl">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold">{{ $isAdmin ? 'All ticket overview' : 'Your ticket overview' }}</h1>
                <p class="mt-1 text-base-content/70">{{ $isAdmin ? 'Monitor support requests across the organization.' : 'Track the status of your support requests.' }}</p>
            </div>
            <a href="{{ route('tickets.create') }}" class="btn btn-primary">Create ticket</a>
        </div>

        <div class="mb-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach (\App\Enums\TicketStatus::cases() as $status)
                <div class="card border border-base-300 bg-base-100 shadow-sm">
                    <div class="card-body p-5">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-base-content/70">{{ str($status->value)->replace('_', ' ')->title() }}</span>
                            <x-ticket-status-badge :status="$status" />
                        </div>
                        <p class="mt-3 text-3xl font-bold">{{ $statusCounts[$status->value] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <section>
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-semibold">Recent tickets</h2>
                <a href="{{ $isAdmin ? route('admin.tickets.index') : route('tickets.index') }}" class="link link-hover text-sm">View all</a>
            </div>
            <x-ticket-table :tickets="$recentTickets" :admin-view="$isAdmin" />
        </section>
    </div>
</x-app-layout>

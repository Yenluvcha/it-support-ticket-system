<x-app-layout :title="$title">
    <div class="mx-auto max-w-6xl">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold">{{ $title }}</h1>
                <p class="mt-1 text-base-content/70">
                    {{ $adminView ? 'Review every support ticket across the system.' : 'Track tickets you have submitted to IT support.' }}
                </p>
            </div>
            <a href="{{ route('tickets.create') }}" class="btn btn-primary">Create ticket</a>
        </div>

        <x-ticket-table :tickets="$tickets" :admin-view="$adminView" />

        <div class="mt-6">
            {{ $tickets->links() }}
        </div>
    </div>
</x-app-layout>

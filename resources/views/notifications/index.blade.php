<x-app-layout title="Notifications">
    <div class="mx-auto max-w-4xl">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold">Notifications</h1>
                <p class="mt-1 text-base-content/70">Updates about your IT support tickets.</p>
            </div>
            @if ($notifications->contains(fn ($notification) => $notification->unread()))
                <form action="{{ route('notifications.read-all') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-ghost">Mark all as read</button>
                </form>
            @endif
        </div>

        <div class="overflow-hidden rounded-box border border-base-300 bg-base-100">
            @forelse ($notifications as $notification)
                <article class="flex flex-col gap-4 border-b border-base-300 p-5 last:border-b-0 sm:flex-row sm:items-start sm:justify-between {{ $notification->unread() ? 'bg-primary/5' : '' }}">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="font-semibold">{{ $notification->data['title'] }}</h2>
                            @if ($notification->unread())
                                <span class="badge badge-primary badge-sm">New</span>
                            @endif
                        </div>
                        <p class="mt-1 text-sm text-base-content/75">{{ $notification->data['message'] }}</p>
                        <p class="mt-2 text-xs text-base-content/60">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex shrink-0 items-center gap-2">
                        <a href="{{ $notification->data['url'] }}" class="btn btn-sm btn-ghost">View ticket</a>
                        @if ($notification->unread())
                            <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-outline">Mark read</button>
                            </form>
                        @endif
                    </div>
                </article>
            @empty
                <p class="p-10 text-center text-base-content/60">You have no notifications.</p>
            @endforelse
        </div>

        <div class="mt-6">{{ $notifications->links() }}</div>
    </div>
</x-app-layout>

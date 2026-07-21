<div class="drawer lg:drawer-open">
    <input id="my-drawer-4" type="checkbox" class="drawer-toggle" />
    <div class="drawer-content">
        <!-- Navbar -->
        <x-partials.navbar />
        <!-- Page content here -->
        <div class="p-4">
            <x-flash-message />
            {{ $slot }}
        </div>
    </div>

    <div class="drawer-side is-drawer-close:overflow-visible">
        <label for="my-drawer-4" aria-label="close sidebar" class="drawer-overlay"></label>

        <div class="flex flex-col min-h-full bg-base-200 is-drawer-close:w-14 is-drawer-open:w-64">
            <!-- Main Menu -->
            <ul class="w-full menu grow pt-2">
                <li>
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'menu-active' : '' }}">
                        <span class="is-drawer-close:hidden">Dashboard</span>
                        <span class="is-drawer-open:hidden">D</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('tickets.index') }}" class="{{ request()->routeIs('tickets.index', 'tickets.show') ? 'menu-active' : '' }}">
                        <span class="is-drawer-close:hidden">My Tickets</span>
                        <span class="is-drawer-open:hidden">T</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('tickets.create') }}" class="{{ request()->routeIs('tickets.create') ? 'menu-active' : '' }}">
                        <span class="is-drawer-close:hidden">Create Ticket</span>
                        <span class="is-drawer-open:hidden">+</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('notifications.index') }}" class="{{ request()->routeIs('notifications.*') ? 'menu-active' : '' }}">
                        <span class="is-drawer-close:hidden">Notifications</span>
                        <span class="is-drawer-open:hidden">N</span>
                        @if (auth()->user()->unreadNotifications()->count())
                            <span class="badge badge-primary badge-sm is-drawer-close:hidden">{{ auth()->user()->unreadNotifications()->count() }}</span>
                        @endif
                    </a>
                </li>
                @if (auth()->user()->isAdmin())
                    <li>
                        <a href="{{ route('admin.tickets.index') }}" class="{{ request()->routeIs('admin.tickets.*') ? 'menu-active' : '' }}">
                            <span class="is-drawer-close:hidden">All Tickets</span>
                            <span class="is-drawer-open:hidden">A</span>
                        </a>
                    </li>
                @endif
                {{-- <li>
                    <button class="is-drawer-close:tooltip is-drawer-close:tooltip-right" data-tip="Homepage">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linejoin="round"
                            stroke-linecap="round" stroke-width="2" fill="none" stroke="currentColor"
                            class="my-1.5 inline-block size-4">
                            <path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path>
                            <path
                                d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z">
                            </path>
                        </svg>
                        <span class="is-drawer-close:hidden">Homepage</span>
                    </button>
                </li> --}}
            </ul>

            <!-- Bottom User -->
            <div class="w-full border-t border-base-300">
                <ul class="menu w-full">
                    <li>
                        <div class="dropdown dropdown-top is-drawer-open:dropdown-center p-0">
                            <div tabindex="0" role="button"
                                class="btn btn-ghost gap-2 hover:border-0 active:bg-black active:text-white w-full px-3 is-drawer-close:justify-center is-drawer-open:justify-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="my-1.5 inline-block size-4"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                                <span class="is-drawer-close:hidden font-normal">
                                    {{ auth()->user()->name }}
                                </span>
                            </div>
                            <ul tabindex="-1"
                                class="dropdown-content menu bg-base-100 rounded-box z-1 w-60 is-drawer-close:w-52 p-2 shadow-sm mb-2.5">
                                <li>
                                    <form action="/logout" method="POST" class="block w-full p-0">
                                        @csrf
                                        <button type="submit" class="w-full px-3 py-1.5 text-left cursor-pointer">
                                            Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

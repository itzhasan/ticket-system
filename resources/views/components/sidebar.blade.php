<div x-data="{
    open: true,
    toggle() {
        this.open = !this.open;
        $dispatch('sidebar-toggled', { open: this.open });
    }
}" class="relative">
    @auth
    <!-- Toggle Button -->
    <button
        @click="toggle()"
        class="fixed z-50 p-2 m-4 bg-gray-800 text-white rounded shadow-lg hover:bg-gray-700 focus:outline-none transition-all duration-300"
        :class="{ 'left-64': open, 'left-4': !open }">
        <span x-show="!open" class="flex items-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </span>
        <span x-show="open" class="flex items-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </span>
    </button>

    <!-- Sidebar Panel -->
    <div
        class="fixed left-0 top-0 h-screen bg-gray-800 z-40 transition-transform duration-300 ease-in-out flex flex-col"
        :class="{ 'w-64 translate-x-0': open, 'w-64 -translate-x-full': !open }">

        <!-- Sidebar Content - No top padding -->
        <div class="text-white p-4 pt-0 overflow-hidden flex-1">
            <nav>
                <ul class="space-y-4 mt-5">
                    <li>
                        <a href="{{ route('dashboard') }}" class="block py-2 px-4 rounded hover:bg-gray-700 transition-colors duration-200"><span class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                </svg>
                                Dashboard
                            </span></a>
                    </li>
                    <!-- Ticket Options Dropdown -->
                    <li>
                        <div x-data="{ ticketOpen: true }" class="relative">
                            <button @click="ticketOpen = !ticketOpen"
                                class="w-full flex items-center justify-between px-4 py-2 text-left text-white bg-gray-800 rounded hover:bg-gray-700 transition duration-300">
                                <span class="flex items-center">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                    </svg>
                                    Ticket Options
                                </span>
                                <svg :class="{'rotate-180': ticketOpen}" class="w-4 h-4 transform transition-transform duration-200"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="ticketOpen" x-transition class="mt-2 pl-8 space-y-2">

                                <a href="{{ route('tickets')}}" class="block py-2 px-4 rounded hover:bg-gray-700 transition-colors duration-200">Tickets</a>
                                <a href="#" class="block py-2 px-4 rounded hover:bg-gray-700 transition-colors duration-200">Templates</a>
                            </div>
                        </div>
                    </li>

                    <!-- Admin Options Dropdown -->
                    @if(auth()->user()->isAdmin())
                    <li class="border-t border-gray-700 pt-4">
                        <div x-data="{ adminOpen: true }" class="relative">
                            <button @click="adminOpen = !adminOpen"
                                class="w-full flex items-center justify-between px-4 py-2 text-left text-white bg-gray-800 rounded hover:bg-gray-700 transition duration-300">
                                <span class="flex items-center font-semibold text-gray-300 tracking-wider uppercase text-sm">
                                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                        </path>
                                    </svg>
                                    Admin Options
                                </span>
                                <svg :class="{'rotate-180': adminOpen}" class="w-4 h-4 transform transition-transform duration-200 text-gray-400"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="adminOpen" x-transition class="mt-2 pl-8 space-y-2">
                                <div x-data="{ ticketOpen: true }" class="relative">
                                    <button @click="ticketOpen = !ticketOpen"
                                        class="w-full flex items-center justify-between px-4 py-2 text-left text-white bg-gray-800 rounded hover:bg-gray-700 transition duration-300">
                                        <span class="flex items-center">
                                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                            </svg>
                                            Ticket Options
                                        </span>
                                        <svg :class="{'rotate-180': ticketOpen}" class="w-4 h-4 transform transition-transform duration-200"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div x-show="ticketOpen" x-transition class="mt-2 pl-8 space-y-2">
                                        <a href="{{ route('categories') }}" class="block py-2 px-4 rounded hover:bg-gray-700 transition-colors duration-200">Categories & Templates</a>
                                    </div>
                                    <a href="{{ route('admin.users') }}" class="block py-2 px-4 rounded hover:bg-gray-700 transition-colors duration-200">Manage Users</a>
                                </div>
                            </div>
                    </li>
                    @endif

                </ul>
            </nav>
        </div>

        <!-- Logout Button -->
        <div class="p-4 border-t border-gray-700">
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center py-2 px-4 bg-red-600 text-white rounded hover:bg-red-700 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </div>
    @endauth
</div>

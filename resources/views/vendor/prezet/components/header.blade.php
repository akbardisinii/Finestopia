<header
    class="sticky top-0 z-50 flex flex-none flex-wrap items-center justify-between bg-white px-4 py-5 shadow-md shadow-gray-900/5 transition duration-500 sm:px-6 lg:px-8"
>
    <div class="relative flex flex-grow basis-0 items-center">
        <button
            aria-label="Menu"
            class="mr-4 rounded-none p-1.5 hover:bg-gray-100 active:bg-gray-200 lg:hidden"
            x-on:click="showSidebar = ! showSidebar"
        >
            <svg
                class="h-6 w-6 text-gray-600"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <line x1="4" x2="20" y1="12" y2="12"></line>
                <line x1="4" x2="20" y1="6" y2="6"></line>
                <line x1="4" x2="20" y1="18" y2="18"></line>
            </svg>
        </button>

        <a
            aria-label="Home"
            href="{{ route('prezet.index') }}"
            class="inline-block flex items-center space-x-2"
        >
        <img src="{{ asset('icons/logobaru.png') }}" alt="Logo" class="h-8 w-auto" />
            <span class="text-2xl font-bold text-gray-900">
                Finestopia Blog
            </span>
        </a>
    </div>
</header>

<header class="bg-white shadow-sm border-b border-gray-200">
    <div class="px-6 py-4 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">
            @yield('title', 'Dashboard')
        </h2>

        <div class="flex items-center space-x-4">
            <div class="relative group">
                <button class="flex items-center space-x-2 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                    @if (auth()->user()->profile_picture)
                        <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}"
                             alt="{{ auth()->user()->name }}"
                             class="w-8 h-8 rounded-full object-cover">
                    @else
                        <div class="w-8 h-8 rounded-full bg-teal-600 flex items-center justify-center text-white text-sm font-bold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif

                    <span class="text-gray-700 font-medium hidden sm:inline">
                        {{ auth()->user()->name }}
                    </span>

                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"></path>
                    </svg>
                </button>

                <div class="absolute right-0 w-48 mt-0 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                        <p class="text-xs text-gray-400 mt-1 capitalize">
                            <span class="inline-block px-2 py-1 bg-gray-100 rounded text-gray-700">
                                {{ auth()->user()->role }}
                            </span>
                        </p>
                    </div>

                    <div class="py-2">
                        <a href="{{ route('profile.edit') }}"
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                            My Profile
                        </a>

                        <a href="{{ route('password.change') }}"
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                            Change Password
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition border-t border-gray-100">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

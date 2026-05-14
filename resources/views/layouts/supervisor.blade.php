<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">

<div class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside class="w-64 bg-teal-900 text-white p-6 flex flex-col">
        <h2 class="text-xl font-bold mb-8">Supervisor</h2>

        <nav class="flex-1 space-y-3">
            <a href="{{ route('supervisor.dashboard') }}" class="block hover:bg-slate-800 p-2 rounded transition">Dashboard</a>
            <a href="{{ route('profile.edit') }}" class="block hover:bg-slate-800 p-2 rounded transition">My Profile</a>
            <a href="#" class="block hover:bg-slate-800 p-2 rounded transition">Team Members</a>
            <a href="#" class="block hover:bg-slate-800 p-2 rounded transition">Attendance Review</a>
            <a href="#" class="block hover:bg-slate-800 p-2 rounded transition">Leave Approval</a>
            <a href="#" class="block hover:bg-slate-800 p-2 rounded transition">Reports</a>
        </nav>

        <!-- Logout Button at Bottom -->
        <div class="border-t border-slate-700 pt-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left p-2 rounded hover:bg-red-600 transition font-medium">
                    🚪 Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <main class="flex-1 flex flex-col bg-gray-50">

        <!-- Topbar with Profile -->
        @include('components.topbar')

        {{-- Page Content --}}
        <div class="flex-1 p-6 overflow-auto">
            @yield('content')
        </div>

    </main>

</div>

</body>
</html>

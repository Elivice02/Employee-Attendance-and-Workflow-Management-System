<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HR Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-lg p-6 flex flex-col">
        <h2 class="text-2xl font-bold text-teal-600 mb-8">HR Panel</h2>

        <nav class="flex-1 space-y-4">
            <a href="{{ route('hr.dashboard') }}" class="block px-3 py-2 rounded-lg hover:bg-teal-50 hover:text-teal-600 transition text-gray-700">Dashboard</a>
            <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-lg hover:bg-teal-50 hover:text-teal-600 transition text-gray-700">My Profile</a>
            <a href="{{ route('hr.employees.create') }}" class="block px-3 py-2 rounded-lg hover:bg-teal-50 hover:text-teal-600 transition text-gray-700">Add User</a>
            <a href="{{ route('hr.employees.index') }}" class="block px-3 py-2 rounded-lg hover:bg-teal-50 hover:text-teal-600 transition text-gray-700">Employees & Supervisors</a>
            <a href="{{ route('hr.attendance.index') }}" class="block px-3 py-2 rounded-lg hover:bg-teal-50 hover:text-teal-600 transition text-gray-700">Attendance</a>
            <a href="{{ route('hr.leaves.index') }}" class="block px-3 py-2 rounded-lg hover:bg-teal-50 hover:text-teal-600 transition text-gray-700">Leave Management</a>
            <a href="{{ route('notifications.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-teal-50 hover:text-teal-600 transition text-gray-700">
                <span>Notifications</span>
                @php($notificationCount = auth()->user()->appNotifications()->unread()->count())
                @if ($notificationCount > 0)
                    <span class="min-w-5 h-5 px-1 rounded-full bg-red-600 text-white text-xs font-bold flex items-center justify-center">
                        {{ $notificationCount > 99 ? '99+' : $notificationCount }}
                    </span>
                @endif
            </a>
        </nav>

        <!-- Logout Button at Bottom -->
        <div class="border-t pt-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-red-600 hover:bg-red-50 transition font-medium">
                    🚪 Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col bg-gray-50">

        <!-- Topbar with Profile -->
        @include('components.topbar')

        <!-- Page Content -->
        <div class="flex-1 p-8 overflow-auto">
            @yield('content')
        </div>

    </main>

</div>

</body>
</html>

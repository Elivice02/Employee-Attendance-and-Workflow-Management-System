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
    <aside class="w-64 bg-teal-900 text-white shadow-lg p-6 flex flex-col">
        <h2 class="text-2xl font-bold mb-8">HR Panel</h2>

        <nav class="flex-1 space-y-4">
            <a href="{{ route('hr.dashboard') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800 transition">Dashboard</a>
            <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800 transition">My Profile</a>
            <a href="{{ route('hr.employees.create') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800 transition">Add User</a>
            <a href="{{ route('hr.employees.index') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800 transition">Employees & Supervisors</a>
            <a href="{{ route('hr.attendance.index') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800 transition">Attendance</a>
            <a href="{{ route('hr.reports.index') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800 transition">Report & Analysis</a>
            <a href="{{ route('hr.leaves.index') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800 transition">Leave Management</a>
            <a href="{{ route('hr.tasks.index') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800 transition">HR Tasks</a>
            <a href="{{ route('hr.daily-log-reviews.index') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800 transition">Supervisor Logs</a>
            <a href="{{ route('hr.announcements.index') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800 transition">Announcements</a>
            <a href="{{ route('notifications.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-slate-800 transition">
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
        <div class="border-t border-slate-700 pt-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2 rounded-lg hover:bg-red-600 transition font-medium">
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

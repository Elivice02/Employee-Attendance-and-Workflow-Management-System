<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
<div class="flex min-h-screen">
    <aside class="w-64 bg-teal-900 text-white shadow-md hidden md:flex flex-col">
        <div class="p-6 border-b border-slate-700">
            <h1 class="text-xl font-bold capitalize">{{ auth()->user()->role }} Panel</h1>
        </div>

        <nav class="flex-1 p-4 space-y-2">
            @if (auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">Dashboard</a>
                <a href="{{ route('admin.hr.index') }}" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">HR Managers</a>
                <a href="{{ route('admin.departments.index') }}" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">Departments</a>
                <a href="{{ route('admin.settings') }}" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">Settings</a>
                <a href="{{ route('hr.announcements.index') }}" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">Announcements</a>
            @elseif (auth()->user()->role === 'hr')
                <a href="{{ route('hr.dashboard') }}" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">Dashboard</a>
                <a href="{{ route('hr.employees.index') }}" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">Employees & Supervisors</a>
                <a href="{{ route('hr.attendance.index') }}" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">Attendance</a>
                <a href="{{ route('hr.reports.index') }}" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">Report & Analysis</a>
                <a href="{{ route('hr.leaves.index') }}" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">Leave Management</a>
                <a href="{{ route('hr.tasks.index') }}" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">HR Tasks</a>
                <a href="{{ route('hr.daily-log-reviews.index') }}" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">Supervisor Logs</a>
                <a href="{{ route('hr.announcements.index') }}" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">Announcements</a>
            @elseif (auth()->user()->role === 'supervisor')
                <a href="{{ route('supervisor.dashboard') }}" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">Dashboard</a>
                <a href="{{ route('supervisor.team-members.index') }}" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">Team Members</a>
                <a href="{{ route('supervisor.tasks.index') }}" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">Task Management</a>
                <a href="{{ route('announcements.index') }}" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">Announcements</a>
            @else
                <a href="{{ route('employee.dashboard') }}" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">Dashboard</a>
                <a href="{{ route('employee.profile') }}" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">Profile</a>
                <a href="{{ route('employee.attendance') }}" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">Attendance</a>
                <a href="{{ route('employee.performance.index') }}" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">My Performance</a>
                <a href="{{ route('employee.leave.index') }}" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">Leave Requests</a>
                <a href="{{ route('employee.tasks.index') }}" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">My Tasks</a>
                <a href="{{ route('employee.daily-logs.index') }}" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">Daily Logs</a>
                <a href="{{ route('announcements.index') }}" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">Announcements</a>
            @endif

            <a href="{{ route('notifications.index') }}" class="flex items-center justify-between px-4 py-2 rounded-lg hover:bg-slate-800 transition">
                <span>Notifications</span>
                @php($notificationCount = auth()->user()->appNotifications()->unread()->count())
                @if ($notificationCount > 0)
                    <span class="min-w-5 h-5 px-1 rounded-full bg-red-600 text-white text-xs font-bold flex items-center justify-center">
                        {{ $notificationCount > 99 ? '99+' : $notificationCount }}
                    </span>
                @endif
            </a>
        </nav>

        <div class="p-4 border-t border-slate-700">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full text-left px-4 py-2 rounded-lg hover:bg-red-600 transition font-medium">
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col bg-gray-50">
        @include('components.topbar')

        <main class="flex-1 p-6 overflow-auto">
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>

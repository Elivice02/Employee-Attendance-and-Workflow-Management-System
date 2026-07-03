<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Employee</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 bg-teal-900 text-white shadow-md hidden md:flex flex-col">
        <div class="p-6 border-b border-slate-700">
            <h1 class="text-xl font-bold">
                Employee Panel
            </h1>
        </div>

        <nav class="flex-1 p-4 space-y-2">

            <a href="{{ route('employee.dashboard') }}"
               class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">
                Dashboard
            </a>

            <a href="{{ route('employee.profile') }}"
               class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">
                Profile
            </a>

            <a href="{{ route('employee.attendance') }}"
               class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">
                Attendance
            </a>

            <a href="{{ route('employee.performance.index') }}"
               class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">
                My Performance
            </a>

            <a href="{{ route('notifications.index') }}"
               class="flex items-center justify-between px-4 py-2 rounded-lg hover:bg-slate-800 transition">
                <span>Notifications</span>
                @php($notificationCount = auth()->user()->appNotifications()->unread()->count())
                @if ($notificationCount > 0)
                    <span class="min-w-5 h-5 px-1 rounded-full bg-red-600 text-white text-xs font-bold flex items-center justify-center">
                        {{ $notificationCount > 99 ? '99+' : $notificationCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('employee.leave.index') }}"
               class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">
                Leave Requests
            </a>

            <a href="{{ route('employee.tasks.index') }}"
               class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">
                My Tasks
            </a>

            <a href="{{ route('employee.daily-logs.index') }}"
               class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">
                Daily Logs
            </a>

            <a href="{{ route('announcements.index') }}"
               class="flex items-center justify-between px-4 py-2 rounded-lg hover:bg-slate-800 transition">
                <span>Announcements</span>
                @php($unreadAnnouncementCount = auth()->user()->getUnreadAnnouncementCount())
                @if ($unreadAnnouncementCount > 0)
                    <span class="min-w-5 h-5 px-1 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center">
                        {{ $unreadAnnouncementCount > 99 ? '99+' : $unreadAnnouncementCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('employee.payroll') }}" 
                class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">
                    Payroll                
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

    <!-- Main Content -->
    <div class="flex-1 flex flex-col bg-gray-50">

        <!-- Topbar with Profile -->
        @include('components.topbar')

        <!-- Page Content -->
        <main class="flex-1 p-6 overflow-auto">
            @yield('content')
        </main>

    </div>

</div>

</body>
</html>

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
    <aside class="w-64 bg-white shadow-md hidden md:flex flex-col">
        <div class="p-6 border-b">
            <h1 class="text-xl font-bold text-gray-800">
                Employee Panel
            </h1>
        </div>

        <nav class="flex-1 p-4 space-y-2">

            <a href="{{ route('employee.dashboard') }}"
               class="block px-4 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
                Dashboard
            </a>

            <a href="{{ route('employee.profile') }}"
               class="block px-4 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
                Profile
            </a>

            <a href="{{ route('employee.attendance') }}"
               class="block px-4 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
                Attendance
            </a>

            <a href="{{ route('notifications.index') }}"
               class="flex items-center justify-between px-4 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
                <span>Notifications</span>
                @php($notificationCount = auth()->user()->appNotifications()->unread()->count())
                @if ($notificationCount > 0)
                    <span class="min-w-5 h-5 px-1 rounded-full bg-red-600 text-white text-xs font-bold flex items-center justify-center">
                        {{ $notificationCount > 99 ? '99+' : $notificationCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('employee.leave.index') }}"
               class="block px-4 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
                Leave Requests
            </a>

            <a href="{{ route('employee.payroll') }}" 
                class="block px-4 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
                    Payroll                
            </a>

        </nav>

        <div class="p-4 border-t">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full text-left px-4 py-2 rounded-lg text-red-600 hover:bg-red-50">
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

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

            <a href="#"
               class="block px-4 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
                Attendance
            </a>

            <a href="#"
               class="block px-4 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
                Leave Requests
            </a>

            <a href="#"
               class="block px-4 py-2 rounded-lg hover:bg-gray-100 text-gray-700">
                Profile
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
    <div class="flex-1 flex flex-col">

        <!-- Topbar -->
        <header class="bg-white shadow-sm p-4 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">
                @yield('title', 'Dashboard')
            </h2>

            <div class="text-sm text-gray-600">
                {{ auth()->user()->name ?? 'Employee' }}
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-6">
            @yield('content')
        </main>

    </div>

</div>

</body>
</html>
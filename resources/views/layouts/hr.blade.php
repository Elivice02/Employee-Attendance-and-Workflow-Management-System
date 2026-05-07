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
    <aside class="w-64 bg-white shadow-lg p-6">
        <h2 class="text-2xl font-bold text-teal-600 mb-8">HR Panel</h2>

        <nav class="space-y-4">
            <a href="{{ route('hr.dashboard') }}" class="block hover:text-teal-600">Dashboard</a>
            <a href="{{ route('hr.employees.create') }}" class="block hover:text-teal-600">Employees</a>
            <a href="{{ route('hr.departments.index') }}" class="block hover:text-teal-600">Departments</a>
        </nav>
    </aside>

    <!-- Main -->
    <main class="flex-1 p-8">
        @yield('content')
    </main>

</div>

</body>
</html>
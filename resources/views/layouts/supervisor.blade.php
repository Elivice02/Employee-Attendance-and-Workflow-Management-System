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
    <aside class="w-64 bg-teal-900 text-white p-6">
        <h2 class="text-xl font-bold mb-8">Supervisor</h2>

        <nav class="space-y-3">
            <a href="#" class="block hover:bg-slate-800 p-2 rounded">Dashboard</a>
            <a href="#" class="block hover:bg-slate-800 p-2 rounded">Team Members</a>
            <a href="#" class="block hover:bg-slate-800 p-2 rounded">Attendance Review</a>
            <a href="#" class="block hover:bg-slate-800 p-2 rounded">Leave Approval</a>
            <a href="#" class="block hover:bg-slate-800 p-2 rounded">Reports</a>
            <a href="#" class="block hover:bg-slate-800 p-2 rounded">Profile</a>
        </nav>
    </aside>

    {{-- Main Content --}}
    <main class="flex-1 p-6">
        @yield('content')
    </main>

</div>

</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-lg p-6 flex flex-col justify-between">
        
        <div>
            <h1 class="text-2xl font-bold text-blue-600 mb-8">Admin Panel</h1>

            <nav class="space-y-2">
                <a href="/admin/dashboard"
                   class="block px-3 py-2 rounded bg-blue-100 text-blue-600 font-medium">
                   Dashboard
                </a>

                <a href="{{ route('profile.edit') }}"
                   class="block px-3 py-2 rounded hover:bg-gray-100">
                   My Profile
                </a>

                <a href="/admin/hr-managers"
                   class="block px-3 py-2 rounded hover:bg-gray-100">
                   HR Managers
                </a>

                <a href="{{ route('admin.departments.index') }}"
                   class="block px-3 py-2 rounded hover:bg-gray-100">
                   Departments
                </a>
            </nav>
        </div>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full bg-red-500 text-white p-2 rounded hover:bg-red-600 transition font-medium">
                🚪 Logout
            </button>
        </form>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8">

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">

            <h2 class="text-3xl font-bold text-gray-800">Dashboard</h2>

            <div class="flex items-center space-x-4">

                <div class="text-right">
                    <p class="font-semibold">{{ auth()->user()->name }}</p>
                    <p class="text-sm text-gray-500 capitalize">
                        {{ auth()->user()->role }}
                    </p>
                    <a href="{{ route('profile.edit') }}" class="text-xs text-blue-600 hover:underline">
                        Edit profile
                    </a>
                </div>

                @if(auth()->user()->profile_picture)
                    <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}"
                        class="w-10 h-10 rounded-full object-cover">
                @else
                    <div class="w-10 h-10 rounded-full bg-blue-500 text-white flex items-center justify-center">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endif

            </div>
        </div>

        <x-alert />

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

            <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition">
                <h3 class="text-gray-500 text-sm">Total Users</h3>
                <p class="text-3xl font-bold mt-2">{{ $totalUsers }}</p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition">
                <h3 class="text-gray-500 text-sm">HR Managers</h3>
                <p class="text-3xl font-bold mt-2">{{ $totalHR }}</p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition">
                <h3 class="text-gray-500 text-sm">Employees</h3>
                <p class="text-3xl font-bold mt-2">{{ $totalEmployees }}</p>
            </div>

        </div>

        <!-- Recent Users -->
        <div class="bg-white p-6 rounded-xl shadow">
            <h3 class="text-lg font-semibold mb-4">Recent Users</h3>

            <table class="w-full text-left">
                <thead>
                    <tr class="text-gray-500 text-sm border-b">
                        <th class="pb-2">Name</th>
                        <th class="pb-2">Email</th>
                        <th class="pb-2">Role</th>
                        <th class="pb-2">Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentUsers as $user)
                        <tr class="border-b text-sm">
                            <td class="py-2">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td class="capitalize">{{ $user->role }}</td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </main>
</div>

</body>
</html>

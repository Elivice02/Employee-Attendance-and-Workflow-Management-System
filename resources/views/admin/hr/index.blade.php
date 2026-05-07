<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HR Managers</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 min-h-screen">

<div class="max-w-6xl mx-auto py-10 px-6">

    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">HR Managers</h1>
            <p class="text-gray-500 text-sm mt-1">
                Manage all HR staff
            </p>
        </div>

        <a href="/admin/hr-managers/create"
           class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow">
            + Add HR Manager
        </a>
    </div>

    <!-- Table Card -->
    <div class="bg-white shadow rounded-xl overflow-hidden">

        <div class="p-4 border-b flex justify-between items-center">
            <h2 class="font-semibold text-gray-700">All HR Managers</h2>
            <span class="text-sm text-gray-400">
                Total: {{ count($hrs) }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">

                <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($hrs as $hr)
                        <tr class="hover:bg-gray-50 transition">

                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $hr->name }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $hr->email }}
                            </td>

                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                    Active
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right space-x-2">

                                <a href="{{ route('admin.hr.edit', $hr->id) }}"
                                   class="text-blue-600 hover:underline text-sm">
                                    Edit
                                </a>

                                <form action="{{ route('admin.hr.destroy', $hr->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline text-sm">
                                        Delete
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-10 text-gray-500">
                                No HR Managers found
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

</div>

</body>
</html>
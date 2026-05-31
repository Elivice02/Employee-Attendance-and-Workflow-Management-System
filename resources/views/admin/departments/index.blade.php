<head>
    <meta charset="UTF-8">
    <title>Departments</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 min-h-screen">

<div class="max-w-7xl mx-auto py-10 px-6">

    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Departments</h1>
            <p class="text-gray-500 text-sm mt-1">
                Manage organization-level departments
            </p>
        </div>

        <a href="{{ route('admin.departments.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow">
            + New Department
        </a>
    </div>

    <x-alert />

    <!-- Table Card -->
    <div class="bg-white shadow rounded-xl overflow-hidden">

        <div class="p-4 border-b flex justify-between items-center">
            <h2 class="font-semibold text-gray-700">All Departments</h2>
            <span class="text-sm text-gray-400">
                Total: {{ count($departments) }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">

                <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Code</th>
                        <th class="px-6 py-3">Description</th>
                        <th class="px-6 py-3">Department Head</th>
                        <th class="px-6 py-3">Employees</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($departments as $dept)
                        <tr class="hover:bg-gray-50 transition">

                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $dept->name }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $dept->code ?? '-' }}
                            </td>

                            <td class="px-6 py-4 text-gray-600 max-w-xs truncate">
                                {{ $dept->description ?? '-' }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $dept->head?->name ?? '-' }}
                            </td>

                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-700">
                                    {{ $dept->employees()->count() }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs rounded-full {{ $dept->isActive() ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ ucfirst($dept->status) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right space-x-2">

                                <a href="{{ route('admin.departments.edit', $dept->id) }}"
                                   class="text-blue-600 hover:underline text-sm">
                                    Edit
                                </a>

                                <form action="{{ route('admin.departments.destroy', $dept->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?')">
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
                            <td colspan="7" class="text-center py-10 text-gray-500">
                                No departments found
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

</div>

</body>

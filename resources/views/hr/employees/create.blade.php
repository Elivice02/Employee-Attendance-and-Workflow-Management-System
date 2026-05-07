<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Employee</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="max-w-2xl mx-auto mt-10 bg-white p-8 rounded shadow">

    <h2 class="text-2xl font-bold mb-6">Create Employee</h2>

    <form method="POST" action="{{ route('hr.employees.store') }}">
        @csrf

        <div class="mb-4">
            <label class="block mb-1">Name</label>
            <input type="text" name="name" class="w-full border p-2 rounded" required>
        </div>

        <div class="mb-4">
            <label class="block mb-1">Email</label>
            <input type="email" name="email" class="w-full border p-2 rounded" required>
        </div>

        <div class="mb-4">
            <label class="block mb-1">Department</label>
            <select name="department_id" class="w-full border p-2 rounded">
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex justify-between">
            <a href="/hr/dashboard" class="text-gray-600">← Back</a>

            <button class="bg-teal-600 text-white px-6 py-2 rounded">
                Create
            </button>
        </div>

    </form>

</div>

</body>
</html>
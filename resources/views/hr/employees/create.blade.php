<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Employee or Supervisor</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="max-w-4xl mx-auto mt-10 bg-white p-8 rounded shadow">

    <h2 class="text-2xl font-bold mb-6">Create Employee or Supervisor</h2>

    <x-alert />

    @if ($errors->any())
        <div class="mb-6 rounded border border-red-200 bg-red-50 p-4 text-red-700">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('hr.employees.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block mb-1">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full border p-2 rounded" required>
            </div>

            <div>
                <label class="block mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full border p-2 rounded" required>
            </div>

            <div>
                <label class="block mb-1">Role</label>
                <select name="role" class="w-full border p-2 rounded" required>
                    <option value="employee" {{ old('role') === 'employee' ? 'selected' : '' }}>Employee</option>
                    <option value="supervisor" {{ old('role') === 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                </select>
            </div>

            <div>
                <label class="block mb-1">Gender</label>
                <select name="gender" class="w-full border p-2 rounded">
                    <option value="">Select gender</option>
                    <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>

            <div>
                <label class="block mb-1">Date of Birth</label>
                <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="w-full border p-2 rounded">
            </div>

            <div>
                <label class="block mb-1">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="0712345678 or +255712345678" class="w-full border p-2 rounded">
                <p class="text-sm text-gray-500 mt-1">Use a Tanzania mobile number. It will be saved as +255 format for SMS.</p>
            </div>

            <div>
                <label class="block mb-1">Department</label>
                <select name="department_id" class="w-full border p-2 rounded">
                    <option value="">No department</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block mb-1">Supervisor</label>
                <select name="supervisor_id" class="w-full border p-2 rounded">
                    <option value="">No supervisor</option>
                    @foreach($supervisors as $supervisor)
                        <option value="{{ $supervisor->id }}" {{ old('supervisor_id') == $supervisor->id ? 'selected' : '' }}>{{ $supervisor->name }}</option>
                    @endforeach
                </select>
                <p class="text-sm text-gray-500 mt-1">Ignored when the new user is a supervisor.</p>
            </div>

            <div>
                <label class="block mb-1">Profile Picture</label>
                <input type="file" name="profile_picture" class="w-full border p-2 rounded">
                <p class="text-sm text-gray-500 mt-1">Upload JPG or PNG, max 2MB.</p>
            </div>
        </div>

        <div class="flex justify-between mt-8">
            <a href="{{ route('hr.employees.index') }}" class="text-gray-600">Back</a>

            <button class="bg-teal-600 text-white px-6 py-2 rounded">
                Create User
            </button>
        </div>

    </form>

</div>

</body>
</html>

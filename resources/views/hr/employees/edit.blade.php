@extends('layouts.hr')

@section('content')
<div class="max-w-4xl bg-white p-8 rounded shadow">
    <h1 class="text-2xl font-bold mb-6">Edit Employee</h1>

    <form method="POST" action="{{ route('hr.employees.update', $employee->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block mb-1">Name</label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $employee->name) }}"
                    class="w-full border p-2 rounded"
                    required
                >
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block mb-1">Email</label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email', $employee->email) }}"
                    class="w-full border p-2 rounded"
                    required
                >
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block mb-1">Role</label>
                <select name="role" class="w-full border p-2 rounded" required>
                    <option value="employee" {{ old('role', $employee->role) == 'employee' ? 'selected' : '' }}>Employee</option>
                    <option value="supervisor" {{ old('role', $employee->role) == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                </select>
                @error('role')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block mb-1">Gender</label>
                <select name="gender" class="w-full border p-2 rounded">
                    <option value="">Select gender</option>
                    <option value="male" {{ old('gender', $employee->gender) == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender', $employee->gender) == 'female' ? 'selected' : '' }}>Female</option>
                </select>
                @error('gender')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block mb-1">Date of Birth</label>
                <input
                    type="date"
                    name="date_of_birth"
                    value="{{ old('date_of_birth', $employee->date_of_birth) }}"
                    class="w-full border p-2 rounded"
                >
                @error('date_of_birth')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block mb-1">Phone Number</label>
                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone', $employee->phone) }}"
                    class="w-full border p-2 rounded"
                >
                @error('phone')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block mb-1">Department</label>
                <select name="department_id" class="w-full border p-2 rounded">
                    <option value="">No department</option>
                    @foreach($departments as $dept)
                        <option
                            value="{{ $dept->id }}"
                            {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}
                        >
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
                @error('department_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block mb-1">Supervisor</label>
                <select name="supervisor_id" class="w-full border p-2 rounded">
                    <option value="">No supervisor</option>
                    @foreach($supervisors as $supervisor)
                        <option
                            value="{{ $supervisor->id }}"
                            {{ old('supervisor_id', $employee->supervisor_id) == $supervisor->id ? 'selected' : '' }}
                        >
                            {{ $supervisor->name }}
                        </option>
                    @endforeach
                </select>
                <p class="text-sm text-gray-500 mt-1">Ignored when this user is a supervisor.</p>
                @error('supervisor_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block mb-1">Profile Picture</label>
                @if($employee->profile_picture)
                    <img
                        src="{{ Storage::url($employee->profile_picture) }}"
                        alt="{{ $employee->name }}"
                        class="h-20 w-20 rounded object-cover mb-3"
                    >
                @endif
                <input type="file" name="profile_picture" class="w-full border p-2 rounded">
                <p class="text-sm text-gray-500 mt-1">Upload JPG or PNG, max 2MB.</p>
                @error('profile_picture')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex justify-between mt-8">
            <a href="{{ route('hr.employees.index') }}" class="text-gray-600 hover:text-gray-900">
                Back
            </a>

            <button class="bg-teal-600 text-white px-6 py-2 rounded hover:bg-teal-700 transition">
                Update
            </button>
        </div>
    </form>
</div>
@endsection

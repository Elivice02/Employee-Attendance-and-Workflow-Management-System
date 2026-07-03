@extends('layouts.employee')

@section('title', 'My Profile')

@section('content')
<div class="container mx-auto px-4 py-8">
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-6 rounded-lg shadow mb-4">
        <form action="{{ route('employee.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Gender</label>
                    <select name="gender" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                        <option value="">Select gender</option>
                        <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="0712345678 or +255712345678" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Use a Tanzania mobile number. It will be saved as +255 format for SMS.</p>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Profile Picture</label>
                    @if($user->profile_picture)
                        <img src="{{ Storage::url($user->profile_picture) }}" alt="{{ $user->name }}" class="h-20 w-20 rounded object-cover mb-3">
                    @endif
                    <input type="file" name="profile_picture" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>

            <div class="mb-4 mt-5">
                <label class="block text-gray-700 font-semibold mb-2">Role</label>
                <input type="text" value="{{ ucfirst($user->role) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100" disabled>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Department</label>
                <input type="text" value="{{ $user->department?->name ?? 'N/A' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100" disabled>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition">
                Update Profile
            </button>
            <a href="{{ route('employee.dashboard') }}" class="ml-2 bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition inline-block">
                Back
            </a>
        </form>
    </div>
</div>
@endsection

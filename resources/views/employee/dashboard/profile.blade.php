@extends('layouts.employee')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">My Profile</h1>

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
        <form action="{{ route('employee.profile.update') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Name</label>
                <input type="text" name="name" value="{{ $user->name }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Email</label>
                <input type="email" value="{{ $user->email }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100" disabled>
                <p class="text-sm text-gray-500 mt-1">Email cannot be changed</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Phone</label>
                <input type="text" name="phone" value="{{ $user->phone ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Address</label>
                <textarea name="address" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" rows="4">{{ $user->address ?? '' }}</textarea>
            </div>

            <div class="mb-4">
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

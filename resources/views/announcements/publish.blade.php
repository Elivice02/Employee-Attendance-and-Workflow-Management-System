@extends('layouts.app')

@section('title', 'Publish Announcement')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Publish Announcement</h1>
        <p class="text-gray-600 mt-1">Select your audience and publish</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ $announcement->title }}</h2>
        <div class="prose prose-sm max-w-none mb-4">
            {!! nl2br(e($announcement->message)) !!}
        </div>

        @if ($announcement->send_sms)
            <div class="alert alert-info mb-4">
                ✓ SMS notifications will be sent to recipients
            </div>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Select Target Audience</h3>

        <form action="{{ route('hr.announcements.publish', $announcement) }}" method="POST">
            @csrf

            <div class="space-y-4 mb-6">
                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition">
                    <input type="checkbox" id="role-employee" name="target_roles[]" value="employee" @checked(in_array('employee', old('target_roles', []), true)) class="w-4 h-4 rounded border-gray-300">
                    <div class="ml-3">
                        <p class="font-medium text-gray-900">Employees</p>
                        <p class="text-sm text-gray-600">All staff members</p>
                    </div>
                </label>

                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition">
                    <input type="checkbox" id="role-supervisor" name="target_roles[]" value="supervisor" @checked(in_array('supervisor', old('target_roles', []), true)) class="w-4 h-4 rounded border-gray-300">
                    <div class="ml-3">
                        <p class="font-medium text-gray-900">Supervisors</p>
                        <p class="text-sm text-gray-600">Team leads and supervisors</p>
                    </div>
                </label>

                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition">
                    <input type="checkbox" id="role-hr" name="target_roles[]" value="hr" @checked(in_array('hr', old('target_roles', []), true)) class="w-4 h-4 rounded border-gray-300">
                    <div class="ml-3">
                        <p class="font-medium text-gray-900">HR Staff</p>
                        <p class="text-sm text-gray-600">Human Resources team members</p>
                    </div>
                </label>
            </div>

            @error('target_roles')
                <p class="text-red-500 text-sm mb-4">{{ $message }}</p>
            @enderror

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-blue-800">
                    <strong>Preview:</strong> This announcement will be sent to all users in the selected roles.
                </p>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn btn-success">
                    Publish Now
                </button>
                <a href="{{ route('hr.announcements.edit', $announcement) }}" class="btn btn-ghost">
                    Back to Edit
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

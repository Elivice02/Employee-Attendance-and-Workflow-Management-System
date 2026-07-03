@extends('layouts.app')

@section('title', 'Edit Announcement')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Edit Announcement</h1>
        <p class="text-gray-600 mt-1">Update your announcement before publishing</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-error mb-4">{{ session('error') }}</div>
    @endif

    <form action="{{ route('hr.announcements.update', $announcement) }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @csrf
        @method('PUT')

        <div class="mb-6">
            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
            <input type="text" id="title" name="title" value="{{ old('title', $announcement->title) }}" placeholder="Enter announcement title" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror"
                   required>
            @error('title')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
            <textarea id="message" name="message" rows="8" placeholder="Write your announcement message here..." 
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('message') border-red-500 @enderror"
                      required>{{ old('message', $announcement->message) }}</textarea>
            @error('message')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="send_sms" value="1" {{ old('send_sms', $announcement->send_sms) ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300">
                <span class="text-sm font-medium text-gray-700">Send SMS notifications to recipients</span>
            </label>
            <p class="text-xs text-gray-500 mt-1">SMS will be sent when announcement is published</p>
        </div>

        <!-- Status Info -->
        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-sm text-yellow-800">
                <strong>Status:</strong> {{ ucfirst($announcement->status) }}<br>
                <strong>Created:</strong> {{ $announcement->created_at->format('M d, Y H:i') }}<br>
                <strong>Created by:</strong> {{ $announcement->creator->name }}
            </p>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn btn-primary">
                Save Changes
            </button>
            @if ($announcement->isDraft())
                <a href="{{ route('hr.announcements.publish-form', $announcement) }}" class="btn btn-success">
                    Publish
                </a>
            @endif
            <a href="{{ route('hr.announcements.index') }}" class="btn btn-ghost">
                Back
            </a>
        </div>
    </form>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Create Announcement')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Create Announcement</h1>
        <p class="text-gray-600 mt-1">Compose a new announcement to send to your team</p>
    </div>

    <form action="{{ route('hr.announcements.store') }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @csrf

        <div class="mb-6">
            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" placeholder="Enter announcement title" 
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
                      required>{{ old('message') }}</textarea>
            @error('message')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="send_sms" value="1" {{ old('send_sms') ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300">
                <span class="text-sm font-medium text-gray-700">Send SMS notifications to recipients</span>
            </label>
            <p class="text-xs text-gray-500 mt-1">SMS will be sent when announcement is published</p>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn btn-primary">
                Save as Draft
            </button>
            <a href="{{ route('hr.announcements.index') }}" class="btn btn-ghost">
                Cancel
            </a>
        </div>
    </form>

    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <p class="text-sm text-blue-800">
            <strong>💡 Tip:</strong> Save this announcement as a draft first. You'll be able to review and choose your target audience before publishing.
        </p>
    </div>
</div>
@endsection

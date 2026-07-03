@extends('layouts.app')

@section('title', $announcement->title)

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <a href="{{ route('announcements.index') }}" class="text-blue-600 hover:underline mb-6 inline-block">
        ← Back to Announcements
    </a>

    <article class="bg-white rounded-lg shadow p-8">
        <header class="mb-6 pb-6 border-b border-gray-200">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $announcement->title }}</h1>
            
            <div class="flex items-center justify-between text-sm text-gray-600">
                <div class="flex items-center gap-4">
                    <span>From: <strong>{{ $announcement->creator->name }}</strong></span>
                    <span>Published: {{ $announcement->published_at->format('M d, Y H:i') }}</span>
                </div>
                @if (is_null($announcement->pivot->read_at))
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        Unread
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        ✓ Read on {{ $announcement->pivot->read_at->format('M d, Y H:i') }}
                    </span>
                @endif
            </div>
        </header>

        <div class="prose prose-lg max-w-none mb-8">
            {!! nl2br(e($announcement->message)) !!}
        </div>

        @if (is_null($announcement->pivot->read_at))
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-800">
                    Mark this announcement as read?
                </p>
                <button onclick="markAsRead({{ $announcement->id }})" class="btn btn-primary btn-sm mt-3">
                    Mark as Read
                </button>
            </div>
        @endif
    </article>
</div>

<script>
function markAsRead(announcementId) {
    fetch(`/announcements/${announcementId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>

@endsection

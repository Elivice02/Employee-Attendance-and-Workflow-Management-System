@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Announcements</h1>
        <p class="text-gray-600 mt-1">Stay updated with important organizational news</p>
    </div>

    @if ($announcements->isEmpty())
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No Announcements</h3>
            <p class="text-gray-600">You don't have any announcements at this time.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($announcements as $announcement)
                <a href="{{ route('announcements.show', $announcement) }}" class="block bg-white rounded-lg shadow hover:shadow-md transition p-6 border-l-4
                    @if(is_null($announcement->pivot->read_at)) border-blue-500 @else border-gray-300 @endif">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $announcement->title }}</h3>
                                @if (is_null($announcement->pivot->read_at))
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        NEW
                                    </span>
                                @endif
                            </div>
                            <p class="text-gray-600 text-sm mb-3">{{ Str::limit($announcement->message, 150) }}</p>
                            <div class="flex items-center gap-4 text-xs text-gray-500">
                                <span>From: {{ $announcement->creator->name }}</span>
                                <span>{{ $announcement->published_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            @if (is_null($announcement->pivot->read_at))
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    Unread
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                    Read
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $announcements->links() }}
        </div>
    @endif
</div>
@endsection

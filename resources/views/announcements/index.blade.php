@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Announcements</h1>
            <p class="text-gray-600 mt-1">Manage organizational announcements</p>
        </div>
        <a href="{{ route('hr.announcements.create') }}" class="btn btn-primary">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Create Announcement
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-error mb-4">{{ session('error') }}</div>
    @endif

    <!-- Filters -->
    <div class="mb-6 flex gap-2">
        <a href="{{ route('hr.announcements.index') }}" class="btn btn-sm {{ request('status') ? 'btn-outline' : 'btn-primary' }}">All</a>
        <a href="{{ route('hr.announcements.index', ['status' => 'draft']) }}" class="btn btn-sm {{ request('status') === 'draft' ? 'btn-primary' : 'btn-outline' }}">Draft</a>
        <a href="{{ route('hr.announcements.index', ['status' => 'published']) }}" class="btn btn-sm {{ request('status') === 'published' ? 'btn-primary' : 'btn-outline' }}">Published</a>
        <a href="{{ route('hr.announcements.index', ['status' => 'archived']) }}" class="btn btn-sm {{ request('status') === 'archived' ? 'btn-primary' : 'btn-outline' }}">Archived</a>
    </div>

    <!-- Announcements Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recipients</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Published</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($announcements as $announcement)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $announcement->title }}</div>
                            <div class="text-sm text-gray-500">By {{ $announcement->creator->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if($announcement->isDraft()) bg-yellow-100 text-yellow-800
                                @elseif($announcement->isPublished()) bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($announcement->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if ($announcement->isPublished())
                                <a href="{{ route('hr.announcements.recipients', $announcement) }}" class="text-blue-600 hover:underline">
                                    {{ $announcement->getRecipientCount() }} recipients
                                </a>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if ($announcement->published_at)
                                {{ $announcement->published_at->format('M d, Y H:i') }}
                            @else
                                <span class="text-gray-500">Not published</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                @if ($announcement->isDraft())
                                    <a href="{{ route('hr.announcements.edit', $announcement) }}" class="btn btn-xs btn-ghost">Edit</a>
                                    <a href="{{ route('hr.announcements.publish-form', $announcement) }}" class="btn btn-xs btn-primary">Publish</a>
                                @elseif (! $announcement->isArchived())
                                    <form method="POST" action="{{ route('hr.announcements.archive', $announcement) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-ghost" onclick="return confirm('Archive this announcement?')">Archive</button>
                                    </form>
                                @endif

                                @if (auth()->user()->role === 'admin')
                                    <form method="POST" action="{{ route('hr.announcements.destroy', $announcement) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-error" onclick="return confirm('Delete this announcement?')">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center">
                            <p class="text-gray-500">No announcements found.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $announcements->links() }}
    </div>
</div>
@endsection

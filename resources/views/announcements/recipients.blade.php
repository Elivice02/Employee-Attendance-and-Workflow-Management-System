@extends('layouts.app')

@section('title', 'Announcement Recipients')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">{{ $announcement->title }}</h1>
        <p class="text-gray-600 mt-1">Recipients and read status</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Total Recipients</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Read</p>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ $readCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Unread</p>
            <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $totalCount - $readCount }}</p>
        </div>
    </div>

    <!-- Progress Bar -->
    @if ($totalCount > 0)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-center mb-2">
                <p class="text-sm font-medium text-gray-700">Read Progress</p>
                <p class="text-sm text-gray-600">{{ round(($readCount / $totalCount) * 100) }}%</p>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-green-500 h-2 rounded-full transition-all" 
                     style="width: {{ ($readCount / $totalCount) * 100 }}%"></div>
            </div>
        </div>
    @endif

    <!-- Recipients Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Read At</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($recipients as $recipient)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $recipient->name }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                @if($recipient->role === 'admin') bg-red-100 text-red-800
                                @elseif($recipient->role === 'hr') bg-blue-100 text-blue-800
                                @elseif($recipient->role === 'supervisor') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($recipient->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            {{ $recipient->department->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4">
                            @if ($recipient->pivot->read_at)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    ✓ Read
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    Unread
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if ($recipient->pivot->read_at)
                                {{ $recipient->pivot->read_at->format('M d, Y H:i') }}
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center">
                            <p class="text-gray-500">No recipients.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $recipients->links() }}
    </div>

    <!-- Back Button -->
    <div class="mt-6">
        <a href="{{ route('hr.announcements.index') }}" class="btn btn-ghost">
            Back to Announcements
        </a>
    </div>
</div>
@endsection

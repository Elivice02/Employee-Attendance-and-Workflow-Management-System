@extends($roleView === 'hr' ? 'layouts.hr' : 'layouts.supervisor')

@section('title', $title)

@section('content')
@php
    $statusColors = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'reviewed' => 'bg-green-100 text-green-800',
        'rejected' => 'bg-red-100 text-red-800',
    ];
@endphp

<div class="max-w-7xl mx-auto px-4 py-8">
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="mb-5 flex items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $title }}</h1>
            <p class="text-sm text-gray-600">Review submitted daily activity reports and record feedback.</p>
        </div>
        @if ($roleView === 'supervisor')
            <a href="{{ route('supervisor.daily-logs.create') }}" class="bg-teal-700 text-white px-4 py-2 rounded hover:bg-teal-800">
                Submit My Daily Log
            </a>
        @endif
    </div>

    <div class="bg-white border rounded-lg shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left">Date</th>
                    <th class="px-4 py-3 text-left">Submitted By</th>
                    <th class="px-4 py-3 text-left">Department</th>
                    <th class="px-4 py-3 text-left">Title</th>
                    <th class="px-4 py-3 text-left">Task</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $log->log_date->format('M d, Y') }}</td>
                        <td class="px-4 py-3 font-semibold">{{ $log->user?->name }}</td>
                        <td class="px-4 py-3">{{ $log->user?->department?->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $log->title }}</td>
                        <td class="px-4 py-3">{{ $log->task?->title ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-3 py-1 rounded-full {{ $statusColors[$log->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route($roleView.'.daily-log-reviews.show', $log) }}" class="bg-slate-700 text-white px-3 py-1 rounded hover:bg-slate-800">Review</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">No daily logs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

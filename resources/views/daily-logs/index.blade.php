@extends($roleView === 'supervisor' ? 'layouts.supervisor' : 'layouts.employee')

@section('title', 'Daily Work Logs')

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
            <h1 class="text-2xl font-bold text-gray-900">Daily Work Logs</h1>
            <p class="text-sm text-gray-600">Record what was done today, linked task progress, and blockers.</p>
        </div>
        <a href="{{ route($roleView.'.daily-logs.create') }}" class="inline-flex items-center justify-center rounded bg-teal-700 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-teal-800">
            Submit Daily Log
        </a>
    </div>

    <div class="bg-white border rounded-lg shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left">Date</th>
                    <th class="px-4 py-3 text-left">Title</th>
                    <th class="px-4 py-3 text-left">Linked Task</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Submitted</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $log->log_date->format('M d, Y') }}</td>
                        <td class="px-4 py-3 font-semibold">{{ $log->title }}</td>
                        <td class="px-4 py-3">{{ $log->task?->title ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-3 py-1 rounded-full {{ $statusColors[$log->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $log->submitted_at?->format('M d, Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route($roleView.'.daily-logs.show', $log) }}" class="bg-slate-700 text-white px-3 py-1 rounded hover:bg-slate-800">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">No daily logs submitted yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

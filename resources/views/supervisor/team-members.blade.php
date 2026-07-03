@extends('layouts.supervisor')

@section('title', 'Team Members')

@section('content')
@php
    $attendanceColors = [
        'present' => 'bg-green-100 text-green-800',
        'late' => 'bg-yellow-100 text-yellow-800',
        'late_pending_review' => 'bg-amber-100 text-amber-800',
        'absent' => 'bg-red-100 text-red-800',
        'on_leave' => 'bg-green-100 text-green-800',
        'not_checked_in' => 'bg-gray-100 text-gray-800',
    ];

    $logColors = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'reviewed' => 'bg-green-100 text-green-800',
        'rejected' => 'bg-red-100 text-red-800',
    ];
@endphp

<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Team Members</h1>
            <p class="text-sm text-gray-600">Monitor assigned employees, attendance, task load, and recent daily logs.</p>
        </div>

        <a href="{{ route('supervisor.tasks.create') }}" class="inline-flex items-center justify-center rounded bg-teal-700 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-teal-800">
            Assign Task
        </a>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="rounded-lg border bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Team Members</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $summary['total'] }}</p>
        </div>
        <div class="rounded-lg border bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Present Today</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $summary['present_today'] }}</p>
        </div>
        <div class="rounded-lg border bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Active Tasks</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $summary['active_tasks'] }}</p>
        </div>
        <div class="rounded-lg border bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Pending Daily Logs</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $summary['pending_logs'] }}</p>
        </div>
    </div>

    <div class="overflow-x-auto rounded-lg border bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left">Employee</th>
                    <th class="px-4 py-3 text-left">Department</th>
                    <th class="px-4 py-3 text-left">Today Attendance</th>
                    <th class="px-4 py-3 text-left">Tasks</th>
                    <th class="px-4 py-3 text-left">Latest Daily Log</th>
                    <th class="px-4 py-3 text-left">Contact</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($teamMembers as $member)
                    @php
                        $attendance = $todayAttendances->get($member->id);
                        $latestLog = $latestDailyLogs->get($member->id);
                        $attendanceStatus = $attendance?->status ?? 'not_checked_in';
                    @endphp
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="font-semibold text-gray-900">{{ $member->name }}</div>
                            <div class="text-xs text-gray-500">{{ ucfirst($member->role) }}</div>
                        </td>
                        <td class="px-4 py-3">{{ $member->department?->name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-3 py-1 {{ $attendanceColors[$attendanceStatus] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst(str_replace('_', ' ', $attendanceStatus)) }}
                            </span>
                            @if ($attendance?->check_in_at)
                                <div class="mt-1 text-xs text-gray-500">In {{ $attendance->check_in_at->format('H:i') }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-gray-900">{{ $member->pending_tasks_count + $member->in_progress_tasks_count }} active</div>
                            <div class="text-xs text-gray-500">
                                {{ $member->pending_tasks_count }} pending, {{ $member->in_progress_tasks_count }} in progress, {{ $member->completed_tasks_count }} completed
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if ($latestLog)
                                <div class="font-semibold text-gray-900">{{ $latestLog->log_date->format('M d, Y') }}</div>
                                <div class="mt-1">
                                    <span class="rounded-full px-3 py-1 {{ $logColors[$latestLog->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($latestLog->status) }}
                                    </span>
                                </div>
                            @else
                                <span class="text-gray-500">No log submitted</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-gray-900">{{ $member->email }}</div>
                            <div class="text-xs text-gray-500">{{ $member->phone ?? '-' }}</div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">No employees are assigned to you yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

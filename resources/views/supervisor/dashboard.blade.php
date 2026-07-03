@extends('layouts.supervisor')

@section('title', 'Supervisor Dashboard')

@section('content')
<div class="space-y-6">
    @include('attendance._widget')

    @include('attendance._notifications', ['attendanceReviewUrl' => route('supervisor.attendance.index')])

    <div>
        <p class="text-sm text-gray-500">Today's team overview</p>
    </div>

    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-100">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Team</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $summary['total_employees'] }}</p>
            <p class="mt-1 text-xs text-gray-500">Assigned employees</p>
        </div>

        <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-100">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Present</p>
            <p class="mt-2 text-2xl font-bold text-green-700">{{ $summary['present_today'] }}</p>
            <p class="mt-1 text-xs text-gray-500">Checked in today</p>
        </div>

        <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-100">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Absent</p>
            <p class="mt-2 text-2xl font-bold text-red-700">{{ $summary['absent_today'] }}</p>
            <p class="mt-1 text-xs text-gray-500">Marked absent today</p>
        </div>

        <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-100">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Reviews</p>
            <p class="mt-2 text-2xl font-bold text-yellow-700">{{ $summary['pending_reviews'] }}</p>
            <p class="mt-1 text-xs text-gray-500">Waiting for action</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-100">
            <div class="border-b border-gray-100 px-5 py-4">
                <h2 class="text-base font-semibold text-gray-900">Team Analysis</h2>
                <p class="mt-1 text-sm text-gray-500">Daily attendance and task progress summary.</p>
            </div>

            <div class="space-y-6 px-5 py-5">
                <div>
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-800">Attendance Today</h3>
                        <span class="text-xs text-gray-400">{{ $summary['total_employees'] }} team members</span>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <div class="mb-1 flex justify-between text-xs text-gray-500">
                                <span>Present</span>
                                <span>{{ $analysis['attendance']['present'] }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-gray-100">
                                <div class="h-2 rounded-full bg-green-500" style="width: {{ $analysis['attendance']['present_percent'] }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="mb-1 flex justify-between text-xs text-gray-500">
                                <span>Absent</span>
                                <span>{{ $analysis['attendance']['absent'] }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-gray-100">
                                <div class="h-2 rounded-full bg-red-500" style="width: {{ $analysis['attendance']['absent_percent'] }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="mb-1 flex justify-between text-xs text-gray-500">
                                <span>Not checked in</span>
                                <span>{{ $analysis['attendance']['not_checked_in'] }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-gray-100">
                                <div class="h-2 rounded-full bg-gray-400" style="width: {{ $analysis['attendance']['not_checked_in_percent'] }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-800">Task Progress</h3>
                        <span class="text-xs text-gray-400">{{ $summary['active_tasks'] }} active</span>
                    </div>

                    <div class="flex h-3 overflow-hidden rounded-full bg-gray-100">
                        <div class="bg-yellow-400" style="width: {{ $analysis['tasks']['pending_percent'] }}%"></div>
                        <div class="bg-blue-500" style="width: {{ $analysis['tasks']['in_progress_percent'] }}%"></div>
                        <div class="bg-green-500" style="width: {{ $analysis['tasks']['completed_percent'] }}%"></div>
                    </div>

                    <div class="mt-3 grid grid-cols-3 gap-2 text-xs text-gray-500">
                        <div><span class="font-semibold text-gray-800">{{ $analysis['tasks']['pending'] }}</span> pending</div>
                        <div><span class="font-semibold text-gray-800">{{ $analysis['tasks']['in_progress'] }}</span> progress</div>
                        <div><span class="font-semibold text-gray-800">{{ $analysis['tasks']['completed'] }}</span> done</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-100">
            <h2 class="text-base font-semibold text-gray-900 mb-4">Recent Activity</h2>

            @if ($recentActivities->isEmpty())
                <p class="text-sm text-gray-600">No recent activities.</p>
            @else
                <ul class="space-y-3 text-sm text-gray-600">
                    @foreach ($recentActivities->take(5) as $activity)
                        <li class="flex items-start justify-between gap-4">
                            <span>
                                {{ $activity['label'] }}
                                <span class="block text-xs text-gray-400">{{ $activity['meta'] }}</span>
                            </span>
                            <span class="shrink-0 text-xs text-gray-400">{{ $activity['time']?->diffForHumans() }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
</div>
@endsection

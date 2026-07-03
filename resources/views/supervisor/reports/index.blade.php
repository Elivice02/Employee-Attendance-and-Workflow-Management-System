@extends('layouts.supervisor')

@section('title', 'Team Analysis')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Team Analysis</h1>
        <p class="text-sm text-gray-500">Read-only performance overview for employees assigned to you.</p>
    </div>

    <form method="GET" action="{{ route('supervisor.reports.index') }}" class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-100">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div>
                <label class="block text-sm font-semibold text-gray-700">Month</label>
                <input type="month" name="month" value="{{ $filters['month']->format('Y-m') }}"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700">Team Member</label>
                <select name="employee_id" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200">
                    <option value="">All assigned employees</option>
                    @foreach ($teamMembers as $member)
                        <option value="{{ $member->id }}" @selected((string) $filters['employee_id'] === (string) $member->id)>
                            {{ $member->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button class="rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">
                    Apply
                </button>
            </div>
        </div>
    </form>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-100">
            <p class="text-sm text-gray-500">Period</p>
            <p class="mt-2 text-xl font-bold text-gray-900">{{ $periodLabel }}</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-100">
            <p class="text-sm text-gray-500">Attendance Rate</p>
            <p class="mt-2 text-3xl font-bold text-teal-700">{{ $attendanceRate }}%</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-100">
            <p class="text-sm text-gray-500">Present</p>
            <p class="mt-2 text-3xl font-bold text-green-700">{{ $statusCounts['present'] }}</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-100">
            <p class="text-sm text-gray-500">Absent</p>
            <p class="mt-2 text-3xl font-bold text-red-700">{{ $statusCounts['absent'] }}</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-100">
            <p class="text-sm text-gray-500">Pending Logs</p>
            <p class="mt-2 text-3xl font-bold text-yellow-700">{{ $dailyLogCounts['pending'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-100">
            <h2 class="text-base font-semibold text-gray-900">Attendance Status</h2>
            <div class="mt-4 h-72">
                <canvas id="statusChart"></canvas>
            </div>
        </section>

        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-100">
            <h2 class="text-base font-semibold text-gray-900">Daily Attendance Trend</h2>
            <div class="mt-4 h-72">
                <canvas id="trendChart"></canvas>
            </div>
        </section>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-100">
            <h2 class="text-base font-semibold text-gray-900">Task Progress</h2>
            <div class="mt-4 h-64">
                <canvas id="taskChart"></canvas>
            </div>
        </section>

        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-100">
            <h2 class="text-base font-semibold text-gray-900">Daily Logs</h2>
            <div class="mt-4 h-64">
                <canvas id="logChart"></canvas>
            </div>
        </section>

        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-100">
            <h2 class="text-base font-semibold text-gray-900">Leave Requests</h2>
            <div class="mt-4 h-64">
                <canvas id="leaveChart"></canvas>
            </div>
        </section>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const statusCounts = @json($statusCounts);
    const taskCounts = @json($taskCounts);
    const dailyLogCounts = @json($dailyLogCounts);
    const leaveCounts = @json($leaveCounts);
    const dailyTrend = @json($dailyTrend);

    new Chart(document.getElementById('statusChart'), {
        type: 'pie',
        data: {
            labels: ['Present', 'Late', 'Absent', 'On Leave'],
            datasets: [{ data: [statusCounts.present, statusCounts.late, statusCounts.absent, statusCounts.on_leave], backgroundColor: ['#16a34a', '#eab308', '#dc2626', '#0d9488'] }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    new Chart(document.getElementById('trendChart'), {
        type: 'bar',
        data: {
            labels: dailyTrend.map(item => item.date),
            datasets: [
                { label: 'Present', data: dailyTrend.map(item => item.present), backgroundColor: '#16a34a' },
                { label: 'Late', data: dailyTrend.map(item => item.late), backgroundColor: '#eab308' },
                { label: 'Absent', data: dailyTrend.map(item => item.absent), backgroundColor: '#dc2626' },
                { label: 'On Leave', data: dailyTrend.map(item => item.on_leave), backgroundColor: '#0d9488' },
            ]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } } }
    });

    new Chart(document.getElementById('taskChart'), {
        type: 'doughnut',
        data: { labels: ['Pending', 'In Progress', 'Completed', 'Overdue'], datasets: [{ data: [taskCounts.pending, taskCounts.in_progress, taskCounts.completed, taskCounts.overdue], backgroundColor: ['#eab308', '#2563eb', '#16a34a', '#dc2626'] }] },
        options: { responsive: true, maintainAspectRatio: false }
    });

    new Chart(document.getElementById('logChart'), {
        type: 'doughnut',
        data: { labels: ['Pending', 'Reviewed', 'Rejected'], datasets: [{ data: [dailyLogCounts.pending, dailyLogCounts.reviewed, dailyLogCounts.rejected], backgroundColor: ['#eab308', '#16a34a', '#dc2626'] }] },
        options: { responsive: true, maintainAspectRatio: false }
    });

    new Chart(document.getElementById('leaveChart'), {
        type: 'doughnut',
        data: { labels: ['Pending', 'Approved', 'Rejected'], datasets: [{ data: [leaveCounts.pending, leaveCounts.approved, leaveCounts.rejected], backgroundColor: ['#eab308', '#16a34a', '#dc2626'] }] },
        options: { responsive: true, maintainAspectRatio: false }
    });
</script>
@endsection

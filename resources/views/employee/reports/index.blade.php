@extends('layouts.employee')

@section('title', 'My Performance')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">My Performance</h1>
        <p class="text-sm text-gray-500">Your personal attendance, task, daily log, and leave performance.</p>
    </div>

    <form method="GET" action="{{ route('employee.performance.index') }}" class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-100">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
            <div>
                <label class="block text-sm font-semibold text-gray-700">Month</label>
                <input type="month" name="month" value="{{ $filters['month']->format('Y-m') }}"
                    class="mt-1 rounded-lg border border-gray-300 px-3 py-2 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200">
            </div>

            <button class="rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">
                Apply
            </button>
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
            <p class="text-sm text-gray-500">Tasks Done</p>
            <p class="mt-2 text-3xl font-bold text-blue-700">{{ $taskCounts['completed'] }}</p>
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
            <h2 class="text-base font-semibold text-gray-900">My Tasks</h2>
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

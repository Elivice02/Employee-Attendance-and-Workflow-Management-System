@extends('layouts.hr')

@section('title', 'Report & Analysis')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Report & Analysis</h1>
        <p class="text-sm text-gray-500">Analyze attendance by month, department, or employee and generate PDF reports.</p>
    </div>

    <form method="GET" action="{{ route('hr.reports.index') }}" class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-100">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700">Month</label>
                <input type="month" name="month" value="{{ $filters['month']->format('Y-m') }}"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700">Department</label>
                <select name="department_id" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200">
                    <option value="">All departments</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" @selected((string) $filters['department_id'] === (string) $department->id)>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700">Employee</label>
                <select name="employee_id" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200">
                    <option value="">All employees</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}" @selected((string) $filters['employee_id'] === (string) $employee->id)>
                            {{ $employee->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button class="rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">
                    Apply
                </button>
                <a href="{{ route('hr.reports.attendance.pdf', request()->query()) }}"
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    PDF
                </a>
            </div>
        </div>
    </form>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-100">
            <p class="text-sm text-gray-500">Period</p>
            <p class="mt-2 text-xl font-bold text-gray-900">{{ $periodLabel }}</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-100">
            <p class="text-sm text-gray-500">Present</p>
            <p class="mt-2 text-3xl font-bold text-green-700">{{ $statusCounts['present'] }}</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-100">
            <p class="text-sm text-gray-500">Late</p>
            <p class="mt-2 text-3xl font-bold text-yellow-700">{{ $statusCounts['late'] }}</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-100">
            <p class="text-sm text-gray-500">Absent</p>
            <p class="mt-2 text-3xl font-bold text-red-700">{{ $statusCounts['absent'] }}</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-100">
            <p class="text-sm text-gray-500">Attendance Rate</p>
            <p class="mt-2 text-3xl font-bold text-teal-700">{{ $attendancePercent }}%</p>
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
            <h2 class="text-base font-semibold text-gray-900">Daily Trend</h2>
            <div class="mt-4 h-72">
                <canvas id="trendChart"></canvas>
            </div>
        </section>
    </div>

    <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-100">
        <h2 class="text-base font-semibold text-gray-900">Department Comparison</h2>
        <div class="mt-4 h-80">
            <canvas id="departmentChart"></canvas>
        </div>
    </section>

    <section class="rounded-lg bg-white shadow-sm ring-1 ring-gray-100">
        <div class="border-b border-gray-100 px-5 py-4">
            <h2 class="text-base font-semibold text-gray-900">Employee Breakdown</h2>
            <p class="mt-1 text-sm text-gray-500">Attendance summary for the selected filters.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-5 py-3">Employee</th>
                        <th class="px-5 py-3">Department</th>
                        <th class="px-5 py-3">Present</th>
                        <th class="px-5 py-3">Late</th>
                        <th class="px-5 py-3">Absent</th>
                        <th class="px-5 py-3">On Leave</th>
                        <th class="px-5 py-3">Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($employeeSummary as $employee)
                        <tr>
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $employee['name'] }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $employee['department'] }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $employee['present'] }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $employee['late'] }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $employee['absent'] }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $employee['on_leave'] }}</td>
                            <td class="px-5 py-3 font-semibold text-teal-700">{{ $employee['attendance_percent'] }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-6 text-center text-gray-500">No attendance records found for this report.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const statusCounts = @json($statusCounts);
    const dailyTrend = @json($dailyTrend);
    const departmentSummary = @json($departmentSummary);

    new Chart(document.getElementById('statusChart'), {
        type: 'pie',
        data: {
            labels: ['Present', 'Late', 'Absent', 'On Leave'],
            datasets: [{
                data: [statusCounts.present, statusCounts.late, statusCounts.absent, statusCounts.on_leave],
                backgroundColor: ['#16a34a', '#eab308', '#dc2626', '#0d9488']
            }]
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

    new Chart(document.getElementById('departmentChart'), {
        type: 'bar',
        data: {
            labels: departmentSummary.map(item => item.department),
            datasets: [
                { label: 'Present', data: departmentSummary.map(item => item.present), backgroundColor: '#16a34a' },
                { label: 'Late', data: departmentSummary.map(item => item.late), backgroundColor: '#eab308' },
                { label: 'Absent', data: departmentSummary.map(item => item.absent), backgroundColor: '#dc2626' },
                { label: 'On Leave', data: departmentSummary.map(item => item.on_leave), backgroundColor: '#0d9488' },
            ]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
    });
</script>
@endsection

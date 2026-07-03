<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\Department;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HRReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->filters($request);
        $report = $this->attendanceReport($filters);

        return view('hr.reports.index', [
            ...$report,
            'filters' => $filters,
            'departments' => Department::query()->orderBy('name')->get(),
            'employees' => User::query()
                ->whereIn('role', ['employee', 'supervisor', 'hr'])
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function attendancePdf(Request $request)
    {
        $filters = $this->filters($request);
        $report = $this->attendanceReport($filters);
        $settings = AttendanceSetting::current();

        $pdf = Pdf::loadView('hr.reports.attendance-pdf', [
            ...$report,
            'filters' => $filters,
            'settings' => $settings,
            'generatedBy' => $request->user(),
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('attendance-report-' . $filters['month']->format('Y-m') . '.pdf');
    }

    private function filters(Request $request): array
    {
        $validated = $request->validate([
            'month' => ['nullable', 'date_format:Y-m'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'employee_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $month = isset($validated['month'])
            ? Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth()
            : now()->startOfMonth();

        return [
            'month' => $month,
            'department_id' => $validated['department_id'] ?? null,
            'employee_id' => $validated['employee_id'] ?? null,
        ];
    }

    private function attendanceReport(array $filters): array
    {
        $startDate = $filters['month']->copy()->startOfMonth();
        $endDate = $filters['month']->copy()->endOfMonth();

        $records = Attendance::query()
            ->with('user.department')
            ->whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->whereHas('user', function ($query) use ($filters) {
                $query->whereIn('role', ['employee', 'supervisor', 'hr']);

                if ($filters['department_id']) {
                    $query->where('department_id', $filters['department_id']);
                }

                if ($filters['employee_id']) {
                    $query->where('id', $filters['employee_id']);
                }
            })
            ->latest('attendance_date')
            ->get();

        $statusCounts = [
            'present' => $records->where('status', 'present')->count(),
            'late' => $records->filter(fn (Attendance $record) => str_starts_with($record->status, 'late'))->count(),
            'absent' => $records->where('status', 'absent')->count(),
            'on_leave' => $records->where('status', 'on_leave')->count(),
        ];

        $totalRecords = max(1, array_sum($statusCounts));
        $attendancePercent = round((($statusCounts['present'] + $statusCounts['late']) / $totalRecords) * 100);

        $departmentSummary = $records
            ->groupBy(fn (Attendance $record) => $record->user?->department?->name ?? 'No department')
            ->map(fn ($items, $name) => [
                'department' => $name,
                'present' => $items->where('status', 'present')->count(),
                'late' => $items->filter(fn (Attendance $record) => str_starts_with($record->status, 'late'))->count(),
                'absent' => $items->where('status', 'absent')->count(),
                'on_leave' => $items->where('status', 'on_leave')->count(),
            ])
            ->sortBy('department')
            ->values();

        $dailyTrend = $records
            ->groupBy(fn (Attendance $record) => $record->attendance_date->format('Y-m-d'))
            ->map(fn ($items, $date) => [
                'date' => Carbon::parse($date)->format('M d'),
                'present' => $items->where('status', 'present')->count(),
                'late' => $items->filter(fn (Attendance $record) => str_starts_with($record->status, 'late'))->count(),
                'absent' => $items->where('status', 'absent')->count(),
                'on_leave' => $items->where('status', 'on_leave')->count(),
            ])
            ->sortBy('date')
            ->values();

        $employeeSummary = $records
            ->groupBy('user_id')
            ->map(function ($items) {
                $user = $items->first()->user;
                $present = $items->where('status', 'present')->count();
                $late = $items->filter(fn (Attendance $record) => str_starts_with($record->status, 'late'))->count();
                $total = max(1, $items->count());

                return [
                    'name' => $user?->name ?? 'Unknown employee',
                    'department' => $user?->department?->name ?? 'No department',
                    'present' => $present,
                    'late' => $late,
                    'absent' => $items->where('status', 'absent')->count(),
                    'on_leave' => $items->where('status', 'on_leave')->count(),
                    'attendance_percent' => round((($present + $late) / $total) * 100),
                ];
            })
            ->sortBy('name')
            ->values();

        return [
            'records' => $records,
            'previewRecords' => $records->take(25),
            'statusCounts' => $statusCounts,
            'attendancePercent' => $attendancePercent,
            'departmentSummary' => $departmentSummary,
            'dailyTrend' => $dailyTrend,
            'employeeSummary' => $employeeSummary,
            'periodLabel' => $startDate->format('F Y'),
        ];
    }
}

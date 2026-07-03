<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\DailyLog;
use App\Models\Leave;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PerformanceReportController extends Controller
{
    public function supervisorTeam(Request $request)
    {
        $supervisor = $request->user();
        $filters = $this->filters($request, allowEmployee: true);
        $teamMembers = $supervisor->employees()
            ->where('role', 'employee')
            ->orderBy('name')
            ->get();

        $teamIds = $teamMembers->pluck('id');

        if ($filters['employee_id'] && $teamIds->contains((int) $filters['employee_id'])) {
            $subjectIds = collect([(int) $filters['employee_id']]);
        } else {
            $subjectIds = $teamIds;
            $filters['employee_id'] = null;
        }

        return view('supervisor.reports.index', [
            ...$this->buildReport($filters['month'], $subjectIds, $supervisor->id),
            'filters' => $filters,
            'teamMembers' => $teamMembers,
        ]);
    }

    public function employeePerformance(Request $request)
    {
        $filters = $this->filters($request, allowEmployee: false);
        $user = $request->user();

        return view('employee.reports.index', [
            ...$this->buildReport($filters['month'], collect([$user->id])),
            'filters' => $filters,
            'user' => $user,
        ]);
    }

    private function filters(Request $request, bool $allowEmployee): array
    {
        $rules = [
            'month' => ['nullable', 'date_format:Y-m'],
        ];

        if ($allowEmployee) {
            $rules['employee_id'] = ['nullable', 'integer', 'exists:users,id'];
        }

        $validated = $request->validate($rules);
        $month = isset($validated['month'])
            ? Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth()
            : now()->startOfMonth();

        return [
            'month' => $month,
            'employee_id' => $validated['employee_id'] ?? null,
        ];
    }

    private function buildReport(Carbon $month, $userIds, ?int $assignedBy = null): array
    {
        $startDate = $month->copy()->startOfMonth();
        $endDate = $month->copy()->endOfMonth();

        $attendanceRecords = Attendance::query()
            ->whereIn('user_id', $userIds)
            ->whereBetween('attendance_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();

        $statusCounts = [
            'present' => $attendanceRecords->where('status', 'present')->count(),
            'late' => $attendanceRecords->filter(fn (Attendance $record) => str_starts_with($record->status, 'late'))->count(),
            'absent' => $attendanceRecords->where('status', 'absent')->count(),
            'on_leave' => $attendanceRecords->where('status', 'on_leave')->count(),
        ];

        $attendanceTotal = max(1, array_sum($statusCounts));
        $attendanceRate = round((($statusCounts['present'] + $statusCounts['late']) / $attendanceTotal) * 100);

        $taskQuery = Task::query()
            ->whereIn('assigned_to', $userIds)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                    ->orWhereBetween('updated_at', [$startDate, $endDate])
                    ->orWhereBetween('due_date', [$startDate->toDateString(), $endDate->toDateString()]);
            });

        if ($assignedBy) {
            $taskQuery->where('assigned_by', $assignedBy);
        }

        $tasks = $taskQuery->get();

        $taskCounts = [
            'pending' => $tasks->where('status', 'pending')->count(),
            'in_progress' => $tasks->where('status', 'in_progress')->count(),
            'completed' => $tasks->where('status', 'completed')->count(),
            'overdue' => $tasks->where('status', 'overdue')->count(),
        ];

        $dailyLogs = DailyLog::query()
            ->whereIn('user_id', $userIds)
            ->whereBetween('log_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();

        $dailyLogCounts = [
            'pending' => $dailyLogs->where('status', 'pending')->count(),
            'reviewed' => $dailyLogs->where('status', 'reviewed')->count(),
            'rejected' => $dailyLogs->where('status', 'rejected')->count(),
        ];

        $leaves = Leave::query()
            ->whereIn('user_id', $userIds)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate->toDateString(), $endDate->toDateString()])
                    ->orWhereBetween('end_date', [$startDate->toDateString(), $endDate->toDateString()]);
            })
            ->get();

        $leaveCounts = [
            'pending' => $leaves->where('status', 'pending')->count(),
            'approved' => $leaves->where('status', 'hr_approved')->count(),
            'rejected' => $leaves->where('status', 'rejected')->count(),
        ];

        $dailyTrend = $attendanceRecords
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

        return [
            'periodLabel' => $startDate->format('F Y'),
            'statusCounts' => $statusCounts,
            'attendanceRate' => $attendanceRate,
            'taskCounts' => $taskCounts,
            'dailyLogCounts' => $dailyLogCounts,
            'leaveCounts' => $leaveCounts,
            'dailyTrend' => $dailyTrend,
        ];
    }
}

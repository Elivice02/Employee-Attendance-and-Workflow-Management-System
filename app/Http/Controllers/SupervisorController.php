<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\DailyLog;
use App\Models\Leave;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupervisorController extends Controller
{
    public function dashboard()
    {
        $supervisor = auth()->user();

        $employees = $supervisor->employees()
            ->where('role', 'employee')
            ->with('department')
            ->get();
        $employeeIds = $employees->pluck('id');

        $todayAttendances = Attendance::query()
            ->whereIn('user_id', $employeeIds)
            ->whereDate('attendance_date', today())
            ->get()
            ->keyBy('user_id');

        $activeTasks = Task::query()
            ->where('assigned_by', $supervisor->id)
            ->whereIn('assigned_to', $employeeIds)
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();

        $pendingDailyLogs = DailyLog::query()
            ->whereIn('user_id', $employeeIds)
            ->where('status', 'pending')
            ->count();

        $pendingLeaves = Leave::query()
            ->whereIn('user_id', $employeeIds)
            ->where('status', 'pending')
            ->count();

        $taskBreakdown = Task::query()
            ->where('assigned_by', $supervisor->id)
            ->whereIn('assigned_to', $employeeIds)
            ->selectRaw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending")
            ->selectRaw("SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress")
            ->selectRaw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed")
            ->first();

        $presentToday = $todayAttendances->whereNotNull('check_in_at')->count();
        $absentToday = $todayAttendances->where('status', 'absent')->count();
        $onLeaveToday = $todayAttendances->where('status', 'on_leave')->count();
        $notCheckedInToday = max(0, $employees->count() - $presentToday - $absentToday - $onLeaveToday);
        $attendanceTotal = max(1, $employees->count());
        $taskTotal = max(1, (int) $taskBreakdown->pending + (int) $taskBreakdown->in_progress + (int) $taskBreakdown->completed);

        $recentActivities = $this->recentActivities($supervisor, $employeeIds);

        $attendanceData = AttendanceController::dashboardDataFor($supervisor);
        $lateNotifications = AttendanceController::notificationsFor($supervisor);

        return view('supervisor.dashboard', [
            'employees' => $employees,
            'todayAttendances' => $todayAttendances,
            'summary' => [
                'total_employees' => $employees->count(),
                'present_today' => $presentToday,
                'absent_today' => $absentToday,
                'active_tasks' => $activeTasks,
                'pending_reviews' => $pendingDailyLogs + $pendingLeaves,
                'pending_daily_logs' => $pendingDailyLogs,
                'pending_leaves' => $pendingLeaves,
            ],
            'analysis' => [
                'attendance' => [
                    'present' => $presentToday,
                    'absent' => $absentToday,
                    'on_leave' => $onLeaveToday,
                    'not_checked_in' => $notCheckedInToday,
                    'present_percent' => round(($presentToday / $attendanceTotal) * 100),
                    'absent_percent' => round(($absentToday / $attendanceTotal) * 100),
                    'on_leave_percent' => round(($onLeaveToday / $attendanceTotal) * 100),
                    'not_checked_in_percent' => round(($notCheckedInToday / $attendanceTotal) * 100),
                ],
                'tasks' => [
                    'pending' => (int) $taskBreakdown->pending,
                    'in_progress' => (int) $taskBreakdown->in_progress,
                    'completed' => (int) $taskBreakdown->completed,
                    'pending_percent' => round(((int) $taskBreakdown->pending / $taskTotal) * 100),
                    'in_progress_percent' => round(((int) $taskBreakdown->in_progress / $taskTotal) * 100),
                    'completed_percent' => round(((int) $taskBreakdown->completed / $taskTotal) * 100),
                ],
            ],
            'recentActivities' => $recentActivities,
            'lateNotifications' => $lateNotifications,
            ...$attendanceData,
        ]);
    }

    private function recentActivities(User $supervisor, $employeeIds)
    {
        $taskActivities = Task::query()
            ->with('assignee')
            ->where('assigned_by', $supervisor->id)
            ->whereIn('assigned_to', $employeeIds)
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(fn (Task $task) => [
                'label' => 'Task "' . $task->title . '" is ' . str_replace('_', ' ', $task->status),
                'meta' => $task->assignee?->name ?? 'Team member',
                'time' => $task->updated_at,
            ]);

        $dailyLogActivities = DailyLog::query()
            ->with('user')
            ->whereIn('user_id', $employeeIds)
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(fn (DailyLog $dailyLog) => [
                'label' => 'Daily log "' . $dailyLog->title . '" is ' . str_replace('_', ' ', $dailyLog->status),
                'meta' => $dailyLog->user?->name ?? 'Team member',
                'time' => $dailyLog->updated_at,
            ]);

        $leaveActivities = Leave::query()
            ->with('employee')
            ->whereIn('user_id', $employeeIds)
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(fn (Leave $leave) => [
                'label' => ucfirst(str_replace('_', ' ', $leave->leave_type)) . ' leave is ' . str_replace('_', ' ', $leave->status),
                'meta' => $leave->employee?->name ?? 'Team member',
                'time' => $leave->updated_at,
            ]);

        $attendanceActivities = Attendance::query()
            ->with('user')
            ->whereIn('user_id', $employeeIds)
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(fn (Attendance $attendance) => [
                'label' => 'Attendance marked as ' . str_replace('_', ' ', $attendance->status),
                'meta' => ($attendance->user?->name ?? 'Team member') . ' on ' . $attendance->attendance_date->format('M d, Y'),
                'time' => $attendance->updated_at,
            ]);

        return collect()
            ->merge($taskActivities)
            ->merge($dailyLogActivities)
            ->merge($leaveActivities)
            ->merge($attendanceActivities)
            ->sortByDesc('time')
            ->take(8)
            ->values();
    }

    public function teamMembers()
    {
        $supervisor = Auth::user();

        $teamMembers = $supervisor->employees()
            ->where('role', 'employee')
            ->with('department')
            ->withCount([
                'assignedTasks as pending_tasks_count' => fn ($query) => $query->where('status', 'pending'),
                'assignedTasks as in_progress_tasks_count' => fn ($query) => $query->where('status', 'in_progress'),
                'assignedTasks as completed_tasks_count' => fn ($query) => $query->where('status', 'completed'),
                'dailyLogs as pending_daily_logs_count' => fn ($query) => $query->where('status', 'pending'),
            ])
            ->orderBy('name')
            ->get();

        $memberIds = $teamMembers->pluck('id');

        $todayAttendances = Attendance::query()
            ->whereIn('user_id', $memberIds)
            ->whereDate('attendance_date', today())
            ->latest('check_in_at')
            ->get()
            ->keyBy('user_id');

        $latestDailyLogs = DailyLog::with('task')
            ->whereIn('user_id', $memberIds)
            ->latest('log_date')
            ->latest('submitted_at')
            ->get()
            ->unique('user_id')
            ->keyBy('user_id');

        return view('supervisor.team-members', [
            'teamMembers' => $teamMembers,
            'todayAttendances' => $todayAttendances,
            'latestDailyLogs' => $latestDailyLogs,
            'summary' => [
                'total' => $teamMembers->count(),
                'present_today' => $todayAttendances->whereNotNull('check_in_at')->count(),
                'active_tasks' => $teamMembers->sum('pending_tasks_count') + $teamMembers->sum('in_progress_tasks_count'),
                'pending_logs' => $teamMembers->sum('pending_daily_logs_count'),
            ],
        ]);
    }

    public function index()
    {
        $totalEmployees = Employee::count();

        $activeTasks = Task::where('status', 'active')->count();

        $pendingApprovals = Task::where('status', 'pending')->count();

        $alerts = Alert::where('is_read', false)->count();

        $employees = Employee::latest()->take(10)->get();

        $recentAlerts = Alert::latest()->take(5)->get();

        return view('supervisor.dashboard', compact(
            'totalEmployees',
            'activeTasks',
            'pendingApprovals',
            'alerts',
            'employees',
            'recentAlerts'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Leave;
use App\Models\Promotion;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HRDashboardController extends Controller
{
    /**
     * Main HR Dashboard with organizational metrics
     */
    public function index()
    {
        $supervisor = Auth::user();
        abort_unless($supervisor->role === 'hr', 403);

        $stats = [
            'total_employees' => User::where('role', 'employee')->count(),
            'total_supervisors' => User::where('role', 'supervisor')->count(),
            'total_departments' => Department::count(),
            'total_attendance_records' => Attendance::count(),
            'total_tasks' => Task::count(),
            'completed_tasks' => Task::where('status', 'completed')->count(),
            'active_tasks' => Task::where('status', 'in_progress')->count(),
            'pending_tasks' => Task::where('status', 'pending_review')->count(),
        ];

        $topPerformers = $this->getTopPerformers(5);

        return view('hr.dashboard', [
            'stats' => $stats,
            'topPerformers' => $topPerformers,
        ]);
    }

    /**
     * Weekly performance report for specific employee
     */
    public function weeklyReport(User $employee)
    {
        abort_unless(Auth::user()->role === 'hr', 403);

        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $tasks = Task::where('assigned_to', $employee->id)
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->get();

        $stats = [
            'total_tasks' => $tasks->count(),
            'completed_tasks' => $tasks->where('status', 'completed')->count(),
            'completion_rate' => $tasks->count() > 0 ? ($tasks->where('status', 'completed')->count() / $tasks->count() * 100) : 0,
            'average_progress' => $tasks->avg('completion_percentage') ?? 0,
        ];

        return view('hr.weekly-report', [
            'employee' => $employee,
            'tasks' => $tasks,
            'stats' => $stats,
            'week_start' => $startOfWeek,
            'week_end' => $endOfWeek,
        ]);
    }

    /**
     * Monthly performance report
     */
    public function monthlyReport(User $employee, ?int $month = null, ?int $year = null)
    {
        abort_unless(Auth::user()->role === 'hr', 403);

        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();

        $tasks = Task::where('assigned_to', $employee->id)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get();

        $stats = [
            'total_tasks' => $tasks->count(),
            'completed_tasks' => $tasks->where('status', 'completed')->count(),
            'completion_rate' => $tasks->count() > 0 ? ($tasks->where('status', 'completed')->count() / $tasks->count() * 100) : 0,
            'average_progress' => $tasks->avg('completion_percentage') ?? 0,
            'on_time_tasks' => $this->calculateOnTimeCount($tasks),
            'quality_score' => $this->calculateQualityScore($tasks),
        ];

        return view('hr.monthly-report', [
            'employee' => $employee,
            'tasks' => $tasks,
            'stats' => $stats,
            'month' => $month,
            'year' => $year,
        ]);
    }

    /**
     * KPI metrics dashboard
     */
    public function kpiMetrics()
    {
        abort_unless(Auth::user()->role === 'hr', 403);

        $kpis = [
            'completion_rate' => $this->calculateCompletionRate(),
            'on_time_rate' => $this->calculateOnTimeRate(),
            'quality_score' => $this->calculateOverallQuality(),
            'employee_count' => User::where('role', 'employee')->count(),
            'supervisor_count' => User::where('role', 'supervisor')->count(),
        ];

        return view('hr.kpi-metrics', [
            'kpis' => $kpis,
        ]);
    }

    /**
     * Individual employee performance card
     */
    public function employeePerformance(User $employee)
    {
        abort_unless(Auth::user()->role === 'hr', 403);

        $performance = [
            'year' => $this->getYearMetrics($employee),
            'quarter' => $this->getQuarterMetrics($employee),
            'month' => $this->getMonthMetrics($employee),
        ];

        return view('hr.employee-performance', [
            'employee' => $employee,
            'performance' => $performance,
        ]);
    }

    /**
     * Helper: Calculate completion rate
     */
    private function calculateCompletionRate(): float
    {
        $total = Task::count();
        $completed = Task::where('status', 'completed')->count();
        return $total > 0 ? ($completed / $total * 100) : 0;
    }

    /**
     * Helper: Calculate on-time rate
     */
    private function calculateOnTimeRate(): float
    {
        $total = Task::where('status', 'completed')->count();
        $onTime = Task::where('status', 'completed')
            ->whereColumn('completed_at', '<=', 'due_date')
            ->count();
        return $total > 0 ? ($onTime / $total * 100) : 0;
    }

    /**
     * Helper: Calculate overall quality
     */
    private function calculateOverallQuality(): float
    {
        $tasks = Task::where('status', 'completed')->get();
        if ($tasks->isEmpty()) {
            return 0;
        }
        return $tasks->avg('completion_percentage') ?? 0;
    }

    /**
     * Helper: Calculate on-time count for tasks
     */
    private function calculateOnTimeCount($tasks): int
    {
        return $tasks->filter(function ($task) {
            return $task->status === 'completed' && 
                   $task->completed_at && 
                   $task->completed_at->lte($task->due_date);
        })->count();
    }

    /**
     * Helper: Calculate quality score
     */
    private function calculateQualityScore($tasks): float
    {
        return $tasks->avg('completion_percentage') ?? 0;
    }

    /**
     * Helper: Get top performers
     */
    private function getTopPerformers(int $limit = 5): array
    {
        $employees = User::where('role', 'employee')->get();
        $performers = $employees->map(function ($employee) {
            $tasks = Task::where('assigned_to', $employee->id)->get();
            $completedTasks = $tasks->where('status', 'completed')->count();
            $completionRate = $tasks->count() > 0 ? ($completedTasks / $tasks->count() * 100) : 0;
            
            return [
                'employee' => $employee,
                'completion_rate' => $completionRate,
                'total_tasks' => $tasks->count(),
                'completed_tasks' => $completedTasks,
            ];
        })->sortByDesc('completion_rate')->take($limit)->toArray();

        return $performers;
    }

    /**
     * Helper: Get year metrics
     */
    private function getYearMetrics(User $employee): array
    {
        $year = now()->year;
        $startOfYear = Carbon::create($year, 1, 1)->startOfYear();
        $endOfYear = Carbon::create($year, 12, 31)->endOfYear();

        $tasks = Task::where('assigned_to', $employee->id)
            ->whereBetween('created_at', [$startOfYear, $endOfYear])
            ->get();

        return [
            'total' => $tasks->count(),
            'completed' => $tasks->where('status', 'completed')->count(),
            'completion_rate' => $tasks->count() > 0 ? ($tasks->where('status', 'completed')->count() / $tasks->count() * 100) : 0,
            'average_progress' => $tasks->avg('completion_percentage') ?? 0,
        ];
    }

    /**
     * Helper: Get quarter metrics
     */
    private function getQuarterMetrics(User $employee): array
    {
        $quarter = (int) ceil(now()->month / 3);
        $startMonth = ($quarter - 1) * 3 + 1;
        $year = now()->year;

        $startOfQuarter = Carbon::create($year, $startMonth, 1);
        $endOfQuarter = $startOfQuarter->clone()->addMonths(2)->endOfMonth();

        $tasks = Task::where('assigned_to', $employee->id)
            ->whereBetween('created_at', [$startOfQuarter, $endOfQuarter])
            ->get();

        return [
            'total' => $tasks->count(),
            'completed' => $tasks->where('status', 'completed')->count(),
            'completion_rate' => $tasks->count() > 0 ? ($tasks->where('status', 'completed')->count() / $tasks->count() * 100) : 0,
            'average_progress' => $tasks->avg('completion_percentage') ?? 0,
        ];
    }

    /**
     * Helper: Get month metrics
     */
    private function getMonthMetrics(User $employee): array
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $tasks = Task::where('assigned_to', $employee->id)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get();

        return [
            'total' => $tasks->count(),
            'completed' => $tasks->where('status', 'completed')->count(),
            'completion_rate' => $tasks->count() > 0 ? ($tasks->where('status', 'completed')->count() / $tasks->count() * 100) : 0,
            'average_progress' => $tasks->avg('completion_percentage') ?? 0,
        ];
    }
}

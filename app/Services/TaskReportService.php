<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TaskReportService
{
    /**
     * Generate weekly performance report for an employee
     */
    public function generateWeeklyReport(User $employee, Carbon $weekStart): array
    {
        $weekEnd = $weekStart->clone()->endOfWeek();

        $tasks = Task::operational()
            ->where('assigned_to', $employee->id)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->get();

        $completed = $tasks->where('status', 'completed')->count();
        $total = $tasks->count();

        return [
            'employee' => $employee->name,
            'week' => $weekStart->format('M d') . ' - ' . $weekEnd->format('M d'),
            'tasksAssigned' => $total,
            'completed' => $completed,
            'completionRate' => $total > 0 ? round(($completed / $total) * 100) : 0,
            'averageProgress' => round($tasks->avg('completion_percentage') ?? 0),
            'onTimeRate' => $this->calculateOnTimeRate($tasks),
            'quality' => $this->calculateQualityScore($tasks),
            'tasks' => $tasks->map(function ($task) {
                return [
                    'title' => $task->title,
                    'status' => $task->status,
                    'progress' => $task->completion_percentage,
                    'dueDate' => $task->end_date,
                    'completed' => $task->completed_at,
                ];
            })->toArray(),
        ];
    }

    /**
     * Generate monthly performance report for an employee
     */
    public function generateMonthlyReport(User $employee, int $month, int $year): array
    {
        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $tasks = Task::operational()
            ->where('assigned_to', $employee->id)
            ->whereBetween('created_at', [$start, $end])
            ->get();

        return [
            'employee' => $employee->name,
            'department' => $employee->department->name ?? 'N/A',
            'month' => $start->format('F Y'),
            'statistics' => [
                'tasksAssigned' => $tasks->count(),
                'completed' => $tasks->where('status', 'completed')->count(),
                'pending' => $tasks->whereIn('status', ['assigned', 'in_progress'])->count(),
                'inRevision' => $tasks->where('status', 'in_revision')->count(),
            ],
            'metrics' => [
                'completionRate' => $this->calculateCompletionRate($tasks),
                'onTimeRate' => $this->calculateOnTimeRate($tasks),
                'quality' => $this->calculateQualityScore($tasks),
                'consistency' => $this->calculateConsistency($tasks),
                'avgDailyUpdates' => $this->calculateAvgUpdates($tasks),
            ],
            'performance' => $this->ratePerformance($tasks),
            'tasks' => $tasks->map(function ($task) {
                return [
                    'title' => $task->title,
                    'status' => $task->status,
                    'progress' => $task->completion_percentage,
                    'createdAt' => $task->created_at,
                    'dueDate' => $task->end_date,
                ];
            })->toArray(),
        ];
    }

    /**
     * Generate department KPI report
     */
    public function generateDepartmentKPIs(): array
    {
        return [];
    }

    /**
     * Generate individual employee KPI card
     */
    public function generateEmployeeKPIs(User $employee): array
    {
        $thisMonth = Task::operational()
            ->where('assigned_to', $employee->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->get();

        return [
            'employee' => $employee->name,
            'thisMonth' => [
                'completionRate' => $this->calculateCompletionRate($thisMonth),
                'quality' => $this->calculateQualityScore($thisMonth),
                'consistency' => $this->calculateConsistency($thisMonth),
            ],
            'thisQuarter' => [
                'completionRate' => $this->calculateCompletionRateForQuarter($employee),
                'quality' => $this->calculateQualityScoreForQuarter($employee),
            ],
            'trend' => 'improving', // Calculate based on previous months
        ];
    }

    /**
     * Generate performance comparison report
     */
    public function generateComparisonReport($department = null): array
    {
        $employees = User::where('role', 'employee');
        
        if ($department) {
            $employees = $employees->where('department_id', $department);
        }

        $employees = $employees->get();

        $comparisons = [];
        foreach ($employees as $employee) {
            $thisMonth = Task::operational()
                ->where('assigned_to', $employee->id)
                ->whereMonth('created_at', now()->month)
                ->get();

            $comparisons[] = [
                'name' => $employee->name,
                'completionRate' => $this->calculateCompletionRate($thisMonth),
                'quality' => $this->calculateQualityScore($thisMonth),
                'rank' => 0, // Will be set after sorting
            ];
        }

        // Sort by completion rate
        usort($comparisons, function ($a, $b) {
            return $b['completionRate'] <=> $a['completionRate'];
        });

        // Add ranks
        foreach ($comparisons as $key => $item) {
            $comparisons[$key]['rank'] = $key + 1;
        }

        return $comparisons;
    }

    // ============ HELPER METHODS ============

    /**
     * Calculate completion rate
     */
    private function calculateCompletionRate(Collection $tasks): int
    {
        if ($tasks->isEmpty()) {
            return 0;
        }

        $completed = $tasks->where('status', 'completed')->count();
        return round(($completed / $tasks->count()) * 100);
    }

    /**
     * Calculate completion rate for quarter
     */
    private function calculateCompletionRateForQuarter(User $employee): int
    {
        $tasks = Task::operational()
            ->where('assigned_to', $employee->id)
            ->whereMonth('created_at', '>=', now()->quarter() * 3 - 2)
            ->whereMonth('created_at', '<=', now()->quarter() * 3)
            ->get();

        return $this->calculateCompletionRate($tasks);
    }

    /**
     * Calculate on-time rate
     */
    private function calculateOnTimeRate(Collection $tasks): int
    {
        $completed = $tasks->where('status', 'completed');
        
        if ($completed->isEmpty()) {
            return 0;
        }

        $onTime = $completed->filter(function ($task) {
            return $task->completed_at && $task->completed_at->lte($task->end_date);
        })->count();

        return round(($onTime / $completed->count()) * 100);
    }

    /**
     * Calculate quality score
     */
    private function calculateQualityScore(Collection $tasks): int
    {
        if ($tasks->isEmpty()) {
            return 0;
        }

        // Quality based on on-time completion + progress consistency
        $onTime = $tasks->filter(function ($task) {
            return $task->completed_at && $task->completed_at->lte($task->end_date);
        })->count();

        $avgProgress = round($tasks->avg('completion_percentage') ?? 0);
        $onTimeScore = round(($onTime / $tasks->count()) * 100);

        // Average of on-time score and progress
        return round(($onTimeScore + min($avgProgress, 100)) / 2);
    }

    /**
     * Calculate quality score for quarter
     */
    private function calculateQualityScoreForQuarter(User $employee): int
    {
        $tasks = Task::operational()
            ->where('assigned_to', $employee->id)
            ->whereMonth('created_at', '>=', now()->quarter() * 3 - 2)
            ->whereMonth('created_at', '<=', now()->quarter() * 3)
            ->get();

        return $this->calculateQualityScore($tasks);
    }

    /**
     * Calculate consistency (steady progress)
     */
    private function calculateConsistency(Collection $tasks): int
    {
        // Based on daily progress submissions
        $totalProgressRecords = 0;
        $totalDaysNeeded = 0;

        foreach ($tasks as $task) {
            $progressRecords = $task->progress()->count();
            $totalProgressRecords += $progressRecords;

            $start = $task->start_date ?? $task->created_at->toDateString();
            $end = $task->end_date ?? $task->due_date;
            $daysNeeded = $start && $end ? now()->parse($start)->diffInDays(now()->parse($end)) + 1 : 1;
            $totalDaysNeeded += $daysNeeded;
        }

        if ($totalDaysNeeded === 0) {
            return 0;
        }

        return min(100, round(($totalProgressRecords / $totalDaysNeeded) * 100));
    }

    /**
     * Calculate average daily updates
     */
    private function calculateAvgUpdates(Collection $tasks): float
    {
        if ($tasks->isEmpty()) {
            return 0;
        }

        $totalUpdates = $tasks->sum(function ($task) {
            return $task->progress()->count();
        });

        return round($totalUpdates / $tasks->count(), 1);
    }

    /**
     * Rate overall performance
     */
    private function ratePerformance(Collection $tasks): string
    {
        $completionRate = $this->calculateCompletionRate($tasks);
        $quality = $this->calculateQualityScore($tasks);
        $avg = ($completionRate + $quality) / 2;

        if ($avg >= 90) return 'Excellent';
        if ($avg >= 75) return 'Good';
        if ($avg >= 60) return 'Satisfactory';
        return 'Needs Improvement';
    }
}

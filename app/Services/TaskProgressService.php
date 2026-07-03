<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskProgress;
use Illuminate\Support\Collection;

class TaskProgressService
{
    /**
     * Create a progress record
     */
    public function createProgress(Task $task, array $data): TaskProgress
    {
        return $task->progress()->create($data);
    }

    /**
     * Update a progress record
     */
    public function updateProgress(TaskProgress $progress, array $data): TaskProgress
    {
        $progress->update($data);
        return $progress->refresh();
    }

    /**
     * Calculate task completion percentage
     */
    public function calculateCompletion(Task $task): int
    {
        $progressRecords = $task->progress;
        
        if ($progressRecords->isEmpty()) {
            return 0;
        }

        return (int) $progressRecords->avg('completion_percentage');
    }

    /**
     * Update task completion percentage
     */
    public function updateTaskCompletion(Task $task): void
    {
        $completion = $this->calculateCompletion($task);
        $task->update(['completion_percentage' => $completion]);
    }

    /**
     * Get progress summary for a task
     */
    public function getProgressSummary(Task $task): array
    {
        $progressRecords = $task->progress()->orderBy('progress_date')->get();

        return [
            'totalRecords' => $progressRecords->count(),
            'unreviewedCount' => $progressRecords->whereNull('supervisor_reviewed_at')->count(),
            'averageProgress' => round($progressRecords->avg('completion_percentage') ?? 0),
            'records' => $progressRecords,
        ];
    }

    /**
     * Check if all required progress records are submitted
     */
    public function allProgressSubmitted(Task $task): bool
    {
        $start = $task->start_date ?? $task->created_at->toDateString();
        $end = $task->end_date ?? $task->due_date;

        if (!$end) {
            return false;
        }

        $requiredDays = now()->parse($start)->diffInDays(now()->parse($end)) + 1;
        $submittedDays = $task->progress()->count();

        return $submittedDays >= $requiredDays;
    }

    /**
     * Get progress timeline data for display
     */
    public function getTimelineData(Task $task): array
    {
        return $task->progress()
            ->orderBy('progress_date')
            ->get()
            ->map(function ($record) {
                return [
                    'id' => $record->id,
                    'date' => $record->progress_date->format('Y-m-d'),
                    'day' => $record->getDayOfWeek(),
                    'percentage' => $record->completion_percentage,
                    'work' => $record->work_done,
                    'challenges' => $record->challenges,
                    'attachment' => $record->attachment_path,
                    'reviewed' => $record->isReviewed(),
                    'remarks' => $record->remarks,
                ];
            })
            ->toArray();
    }

    /**
     * Calculate progress velocity (daily average change)
     */
    public function getProgressVelocity(Task $task): float
    {
        $records = $task->progress()->orderBy('progress_date')->get();

        if ($records->count() < 2) {
            return 0;
        }

        $firstPercentage = $records->first()->completion_percentage;
        $lastPercentage = $records->last()->completion_percentage;
        $days = $records->first()->progress_date->diffInDays($records->last()->progress_date);

        return $days > 0 ? round(($lastPercentage - $firstPercentage) / $days, 2) : 0;
    }

    /**
     * Predict completion date based on progress velocity
     */
    public function predictCompletionDate(Task $task): ?\DateTime
    {
        $velocity = $this->getProgressVelocity($task);
        
        if ($velocity <= 0) {
            return null;
        }

        $currentProgress = $task->completion_percentage;
        $remaining = 100 - $currentProgress;
        $daysNeeded = ceil($remaining / $velocity);

        return now()->addDays($daysNeeded)->toDateTime();
    }

    /**
     * Check if task is on track
     */
    public function isOnTrack(Task $task): bool
    {
        $start = $task->start_date ?? $task->created_at->toDateString();
        $end = $task->end_date ?? $task->due_date;

        if (!$end) {
            return true;
        }

        $totalDays = now()->parse($start)->diffInDays(now()->parse($end)) + 1;
        $daysElapsed = now()->parse($start)->diffInDays(now()->toDateString()) + 1;
        $expectedProgress = round(($daysElapsed / $totalDays) * 100);
        $actualProgress = $task->completion_percentage;

        return $actualProgress >= ($expectedProgress - 10); // Allow 10% buffer
    }
}

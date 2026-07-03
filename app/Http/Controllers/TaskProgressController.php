<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskProgress;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskProgressController extends Controller
{
    /**
     * Show task progress form
     */
    public function create(Task $task)
    {
        $this->authorize('submitProgress', $task);

        $today = now()->toDateString();
        $existingProgress = $task->progress()
            ->where('employee_id', Auth::id())
            ->where('progress_date', $today)
            ->first();

        return view('tasks.employee-progress-create', [
            'task' => $task->load(['assigner', 'assignee']),
            'progress' => $existingProgress,
            'isUpdate' => $existingProgress !== null,
        ]);
    }

    /**
     * Store task progress
     */
    public function store(Request $request, Task $task)
    {
        $this->authorize('submitProgress', $task);

        $validated = $request->validate([
            'work_done' => ['required', 'string', 'max:2000'],
            'completion_percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'challenges' => ['nullable', 'string', 'max:1000'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx'],
        ]);

        $today = now()->toDateString();

        // Check if progress for this date already exists
        $existingProgress = $task->progress()
            ->where('employee_id', Auth::id())
            ->where('progress_date', $today)
            ->first();

        // Handle file upload
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store(
                "task_progress/{$task->id}",
                'public'
            );
        }

        if ($existingProgress) {
            // Update existing progress
            $existingProgress->update([
                'work_done' => $validated['work_done'],
                'completion_percentage' => $validated['completion_percentage'],
                'challenges' => $validated['challenges'] ?? null,
                'attachment_path' => $attachmentPath ?? $existingProgress->attachment_path,
            ]);
            $progress = $existingProgress;
            $message = 'Task progress updated successfully.';
        } else {
            // Create new progress
            $progress = $task->progress()->create([
                'employee_id' => Auth::id(),
                'progress_date' => $today,
                'work_done' => $validated['work_done'],
                'completion_percentage' => $validated['completion_percentage'],
                'challenges' => $validated['challenges'] ?? null,
                'attachment_path' => $attachmentPath,
            ]);
            $message = 'Task progress submitted successfully.';
        }

        // Update task completion percentage
        $task->updateCompletionPercentage();

        // Notify supervisor of progress update
        $this->notifySupervisor($task, $progress);

        // Check if all required progress records are submitted (if end_date is today)
        if ($task->end_date && now()->toDateString() === $task->end_date->toDateString() && $task->status === 'in_progress') {
            $task->update(['status' => 'pending_review']);
            $this->notifySupervisorForReview($task);
        }

        return redirect()->route('employee.tasks.show', $task)
            ->with('success', $message);
    }

    /**
     * Update task progress (for the same day)
     */
    public function update(Request $request, Task $task, TaskProgress $progress)
    {
        abort_unless($progress->task_id === $task->id, 404);
        $this->authorize('update', $progress);

        $validated = $request->validate([
            'work_done' => ['required', 'string', 'max:2000'],
            'completion_percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'challenges' => ['nullable', 'string', 'max:1000'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx'],
        ]);

        // Handle file upload
        if ($request->hasFile('attachment')) {
            // Delete old attachment if exists
            if ($progress->attachment_path) {
                Storage::disk('public')->delete($progress->attachment_path);
            }
            $attachmentPath = $request->file('attachment')->store(
                "task_progress/{$task->id}",
                'public'
            );
            $validated['attachment_path'] = $attachmentPath;
        }

        $progress->update($validated);

        // Update task completion percentage
        $task->updateCompletionPercentage();

        // Notify supervisor
        $this->notifySupervisor($task, $progress);

        return redirect()->route('employee.tasks.show', $task)
            ->with('success', 'Task progress updated successfully.');
    }

    /**
     * Show progress timeline view (for supervisor)
     */
    public function show(Task $task)
    {
        $this->authorize('viewProgress', $task);

        $progressRecords = $task->progress()
            ->orderBy('progress_date')
            ->get();

        return view('tasks.progress-timeline', [
            'task' => $task->load(['assigner', 'assignee']),
            'progressRecords' => $progressRecords,
        ]);
    }

    /**
     * Supervisor reviews progress record
     */
    public function review(Request $request, Task $task, TaskProgress $progress)
    {
        abort_unless($progress->task_id === $task->id, 404);
        $this->authorize('review', $progress);

        $validated = $request->validate([
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $progress->markAsReviewed(Auth::user(), $validated['remarks'] ?? null);

        return redirect()->route('supervisor.tasks.show', $task)
            ->with('success', 'Progress record reviewed.');
    }

    /**
     * Get progress data for chart display
     */
    public function chart(Task $task)
    {
        $this->authorize('viewProgress', $task);

        $progressRecords = $task->progress()
            ->orderBy('progress_date')
            ->get()
            ->map(function ($record) {
                return [
                    'date' => $record->progress_date->format('M d'),
                    'day' => $record->getDayOfWeek(),
                    'percentage' => $record->completion_percentage,
                    'work' => $record->work_done,
                ];
            });

        return response()->json($progressRecords);
    }

    /**
     * Notify supervisor of progress update
     */
    private function notifySupervisor(Task $task, TaskProgress $progress): void
    {
        $supervisor = $task->assigner;
        
        Notification::create([
            'user_id' => $supervisor->id,
            'type' => 'task_progress',
            'title' => 'Task Progress Update',
            'message' => "{$task->assignee->name} updated progress on '{$task->title}' to {$progress->completion_percentage}%",
            'notifiable_type' => Task::class,
            'notifiable_id' => $task->id,
            'action_url' => route('supervisor.tasks.show', $task),
        ]);
    }

    /**
     * Notify supervisor that task is ready for review
     */
    private function notifySupervisorForReview(Task $task): void
    {
        $supervisor = $task->assigner;

        Notification::create([
            'user_id' => $supervisor->id,
            'type' => 'task_review',
            'title' => 'Task Ready for Review',
            'message' => "'{$task->title}' is ready for your review. All progress records have been submitted.",
            'notifiable_type' => Task::class,
            'notifiable_id' => $task->id,
            'action_url' => route('supervisor.tasks.show', $task),
        ]);
    }
}

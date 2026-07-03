<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Task;
use App\Models\TaskUpdate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * SUPERVISOR: List supervisor's tasks
     */
    public function supervisorIndex()
    {
        $supervisor = Auth::user();
        $tasks = Task::with(['assignee.department', 'assigner'])
            ->where('scope', 'operational')
            ->where(function ($query) use ($supervisor) {
                $query->where('assigned_by', $supervisor->id)
                    ->orWhereIn('assigned_to', $supervisor->employees()->pluck('id'));
            })
            ->latest()
            ->paginate(15);

        return view('tasks.supervisor-index', [
            'tasks' => $tasks,
            'activeCount' => Task::where('assigned_by', $supervisor->id)->active()->count(),
            'completedCount' => Task::where('assigned_by', $supervisor->id)->completed()->count(),
        ]);
    }

    /**
     * SUPERVISOR: Show task creation form
     */
    public function supervisorCreate()
    {
        $this->authorize('create', Task::class);

        $employees = Auth::user()->employees()
            ->where('role', 'employee')
            ->orderBy('name')
            ->get();

        return view('tasks.supervisor-form', [
            'employees' => $employees,
            'title' => 'Create Task',
            'action' => route('supervisor.tasks.store'),
            'method' => 'POST',
            'roleView' => 'supervisor',
        ]);
    }

    /**
     * SUPERVISOR: Store task
     */
    public function supervisorStore(Request $request)
    {
        $this->authorize('create', Task::class);

        $validated = $this->validateTaskCreation($request);

        $task = Task::create([
            ...$validated,
            'assigned_by' => Auth::id(),
            'scope' => 'operational',
            'status' => 'assigned',
        ]);

        $this->notifyTaskAssigned($task);

        return redirect()
            ->route('supervisor.tasks.show', $task)
            ->with('success', 'Task created successfully.');
    }

    /**
     * SUPERVISOR: Show task details
     */
    public function supervisorShow(Task $task)
    {
        $this->authorize('view', $task);

        return view('tasks.supervisor-show', [
            'task' => $task,
            'progress' => $task->progress()->orderBy('progress_date')->get(),
            'unreviewedProgress' => $task->progress()->unreviewed()->count(),
        ]);
    }

    /**
     * SUPERVISOR: Show task edit form
     */
    public function supervisorEdit(Task $task)
    {
        $this->authorize('update', $task);

        $employees = Auth::user()->employees()
            ->where('role', 'employee')
            ->orderBy('name')
            ->get();

        return view('tasks.supervisor-form', [
            'task' => $task,
            'employees' => $employees,
            'title' => 'Edit Task',
            'action' => route('supervisor.tasks.update', $task),
            'method' => 'PUT',
            'roleView' => 'supervisor',
        ]);
    }

    /**
     * SUPERVISOR: Update task
     */
    public function supervisorUpdate(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $this->validateTaskCreation($request);

        $task->update($validated);

        return redirect()
            ->route('supervisor.tasks.show', $task)
            ->with('success', 'Task updated successfully.');
    }

    /**
     * SUPERVISOR: Approve task
     */
    public function supervisorApprove(Request $request, Task $task)
    {
        $this->authorize('approve', $task);

        abort_unless($task->status === 'pending_review', 403);

        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $this->notifyTaskApproved($task, $request->input('remarks'));

        return back()->with('success', 'Task approved successfully.');
    }

    /**
     * SUPERVISOR: Reject task for revision
     */
    public function supervisorReject(Request $request, Task $task)
    {
        $this->authorize('reject', $task);

        abort_unless($task->status === 'pending_review', 403);

        $task->update(['status' => 'in_revision']);

        $this->notifyTaskRejected($task, $request->input('feedback'));

        return back()->with('success', 'Task sent back for revision.');
    }

    /**
     * EMPLOYEE: List my tasks
     */
    public function employeeIndex()
    {
        $employee = Auth::user();
        $tasks = Task::where('assigned_to', $employee->id)
            ->where('status', '!=', 'archived')
            ->latest()
            ->paginate(15);

        return view('tasks.employee-index', [
            'tasks' => $tasks,
            'activeCount' => $tasks->where('status', 'in_progress')->count(),
            'assignedCount' => $tasks->where('status', 'assigned')->count(),
        ]);
    }

    /**
     * EMPLOYEE: Show task details
     */
    public function employeeShow(Task $task)
    {
        $this->authorize('view', $task);

        return view('tasks.employee-show', [
            'task' => $task,
            'canStart' => $task->canStart(),
            'canSubmitProgress' => in_array($task->status, ['in_progress', 'in_revision']),
        ]);
    }

    /**
     * EMPLOYEE: Start task
     */
    public function employeeStart(Task $task)
    {
        $this->authorize('start', $task);

        abort_unless($task->status === 'assigned', 403);
        abort_unless($task->assigned_to === Auth::id(), 403);

        $task->update([
            'status' => 'in_progress',
            'start_date' => now()->toDateString(),
            'started_at' => now(),
        ]);

        $this->notifyTaskStarted($task);

        return redirect()
            ->route('employee.tasks.show', $task)
            ->with('success', 'Task started successfully.');
    }

    /**
     * HR: List compliance tasks
     */
    public function hrIndex()
    {
        $this->authorize('create', Task::class);
        abort_unless(Auth::user()->role === 'hr', 403);

        $tasks = Task::where('scope', 'hr_compliance')
            ->latest()
            ->paginate(15);

        return view('tasks.hr-index', [
            'tasks' => $tasks,
            'totalCount' => $tasks->count(),
        ]);
    }

    /**
     * HR: Show compliance task creation form
     */
    public function hrCreate()
    {
        $this->authorize('create', Task::class);
        abort_unless(Auth::user()->role === 'hr', 403);

        $supervisors = User::where('role', 'supervisor')
            ->orderBy('name')
            ->get();

        return view('tasks.hr-form', [
            'title' => 'Create Compliance Task',
            'action' => route('hr.tasks.store'),
            'method' => 'POST',
            'supervisors' => $supervisors,
        ]);
    }

    /**
     * HR: Store compliance task
     */
    public function hrStore(Request $request)
    {
        $this->authorize('create', Task::class);
        abort_unless(Auth::user()->role === 'hr', 403);

        $validated = $this->validateTaskCreation($request);

        $task = Task::create([
            ...$validated,
            'assigned_by' => Auth::id(),
            'scope' => 'hr_compliance',
            'status' => 'assigned',
        ]);

        $this->notifyComplianceTaskAssigned($task);

        return redirect()
            ->route('hr.tasks.show', $task)
            ->with('success', 'HR compliance task assigned successfully.');
    }

    /**
     * HR: Show compliance task
     */
    public function hrShow(Task $task)
    {
        abort_unless($task->scope === 'hr_compliance', 403);

        return view('tasks.hr-show', [
            'task' => $task,
            'progress' => $task->progress()->orderBy('progress_date')->get(),
        ]);
    }

    /**
     * Store a legacy progress note for task screens that still use task updates.
     */
    public function addUpdate(Request $request, Task $task)
    {
        $this->authorize('view', $task);
        abort_unless(Auth::user()->role === 'employee' && $task->assigned_to === Auth::id(), 403);
        abort_unless(in_array($task->status, ['assigned', 'in_progress', 'in_revision'], true), 422);

        $validated = $request->validate([
            'progress_notes' => ['required', 'string', 'max:3000'],
            'action' => ['required', 'in:note,start,complete'],
        ]);

        $status = $task->status;

        if ($validated['action'] === 'start') {
            abort_unless($task->status === 'assigned', 422);
            $status = 'in_progress';
            $task->update([
                'status' => $status,
                'start_date' => $task->start_date ?? now()->toDateString(),
                'started_at' => $task->started_at ?? now(),
            ]);
        } elseif ($validated['action'] === 'complete') {
            abort_unless(in_array($task->status, ['in_progress', 'in_revision'], true), 422);
            $status = 'pending_review';
            $task->update(['status' => $status]);
        }

        $task->updates()->create([
            'user_id' => Auth::id(),
            'progress_notes' => $validated['progress_notes'],
            'status_after_update' => $status,
        ]);

        return back()->with('success', 'Task update saved successfully.');
    }

    /**
     * Validate task creation inputs
     */
    private function validateTaskCreation(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'required|date|after:today',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'priority' => 'required|in:low,medium,high,critical',
        ]);
    }

    /**
     * Notify employee task was assigned
     */
    private function notifyTaskAssigned(Task $task): void
    {
        $message = "You have been assigned: {$task->title}";

        Notification::create([
            'user_id' => $task->assigned_to,
            'type' => 'task_assigned',
            'title' => 'Task Assigned',
            'message' => $message,
            'data' => json_encode(['task_id' => $task->id]),
        ]);
    }

    /**
     * Notify supervisor task has started
     */
    private function notifyTaskStarted(Task $task): void
    {
        $employee = $task->assignee;
        $message = "{$employee->name} started task: {$task->title}";

        Notification::create([
            'user_id' => $task->assigned_by,
            'type' => 'task_started',
            'title' => 'Task Started',
            'message' => $message,
            'data' => json_encode(['task_id' => $task->id]),
        ]);
    }

    /**
     * Notify employee task was approved
     */
    private function notifyTaskApproved(Task $task, ?string $remarks = null): void
    {
        $message = "Congratulations! Task approved: {$task->title}";
        if ($remarks) {
            $message .= " - {$remarks}";
        }

        Notification::create([
            'user_id' => $task->assigned_to,
            'type' => 'task_approved',
            'title' => 'Task Approved',
            'message' => $message,
            'data' => json_encode(['task_id' => $task->id]),
        ]);
    }

    /**
     * Notify employee task was rejected
     */
    private function notifyTaskRejected(Task $task, ?string $feedback = null): void
    {
        $message = "Revision needed: {$task->title}";
        if ($feedback) {
            $message .= " - {$feedback}";
        }

        Notification::create([
            'user_id' => $task->assigned_to,
            'type' => 'task_rejected',
            'title' => 'Task Revision Needed',
            'message' => $message,
            'data' => json_encode(['task_id' => $task->id]),
        ]);
    }

    /**
     * Notify supervisor of compliance task
     */
    private function notifyComplianceTaskAssigned(Task $task): void
    {
        $message = "HR assigned compliance task: {$task->title}";

        Notification::create([
            'user_id' => $task->assigned_to,
            'type' => 'task_assigned',
            'title' => 'HR Compliance Task',
            'message' => $message,
            'data' => json_encode(['task_id' => $task->id]),
        ]);
    }
}

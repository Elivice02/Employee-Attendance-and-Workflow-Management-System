<?php

namespace App\Http\Controllers;

use App\Models\DailyLog;
use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DailyLogController extends Controller
{
    public function employeeIndex()
    {
        return $this->ownIndex('employee');
    }

    public function employeeCreate()
    {
        return $this->ownCreate('employee');
    }

    public function employeeStore(Request $request)
    {
        return $this->storeOwnLog($request, 'employee');
    }

    public function employeeShow(DailyLog $dailyLog)
    {
        return $this->ownShow($dailyLog, 'employee');
    }

    public function supervisorIndex()
    {
        return $this->ownIndex('supervisor');
    }

    public function supervisorCreate()
    {
        return $this->ownCreate('supervisor');
    }

    public function supervisorStore(Request $request)
    {
        return $this->storeOwnLog($request, 'supervisor');
    }

    public function supervisorShow(DailyLog $dailyLog)
    {
        return $this->ownShow($dailyLog, 'supervisor');
    }

    public function supervisorReviewIndex()
    {
        $employeeIds = Auth::user()->employees()->where('role', 'employee')->pluck('id');

        $logs = DailyLog::with(['user.department', 'task', 'reviews.reviewer'])
            ->whereIn('user_id', $employeeIds)
            ->latest('log_date')
            ->get();

        return view('daily-logs.review-index', [
            'logs' => $logs,
            'roleView' => 'supervisor',
            'title' => 'Employee Daily Logs',
        ]);
    }

    public function supervisorReviewShow(DailyLog $dailyLog)
    {
        abort_unless($dailyLog->user?->supervisor_id === Auth::id(), 403);

        return view('daily-logs.show', [
            'dailyLog' => $dailyLog->load(['user.department', 'task', 'reviews.reviewer']),
            'roleView' => 'supervisor',
            'canReview' => $dailyLog->status === 'pending',
        ]);
    }

    public function supervisorReview(Request $request, DailyLog $dailyLog)
    {
        abort_unless($dailyLog->user?->supervisor_id === Auth::id(), 403);

        return $this->reviewLog($request, $dailyLog, 'supervisor');
    }

    public function hrReviewIndex()
    {
        $logs = DailyLog::with(['user.department', 'task', 'reviews.reviewer'])
            ->whereHas('user', fn ($query) => $query->where('role', 'supervisor'))
            ->latest('log_date')
            ->get();

        return view('daily-logs.review-index', [
            'logs' => $logs,
            'roleView' => 'hr',
            'title' => 'Supervisor Daily Logs',
        ]);
    }

    public function hrReviewShow(DailyLog $dailyLog)
    {
        abort_unless($dailyLog->user?->role === 'supervisor', 403);

        return view('daily-logs.show', [
            'dailyLog' => $dailyLog->load(['user.department', 'task', 'reviews.reviewer']),
            'roleView' => 'hr',
            'canReview' => $dailyLog->status === 'pending',
        ]);
    }

    public function hrReview(Request $request, DailyLog $dailyLog)
    {
        abort_unless($dailyLog->user?->role === 'supervisor', 403);

        return $this->reviewLog($request, $dailyLog, 'hr');
    }

    private function ownIndex(string $roleView)
    {
        $logs = Auth::user()->dailyLogs()
            ->with(['task', 'reviews.reviewer'])
            ->latest('log_date')
            ->get();

        return view('daily-logs.index', compact('logs', 'roleView'));
    }

    private function ownCreate(string $roleView)
    {
        return view('daily-logs.form', [
            'roleView' => $roleView,
            'tasks' => $this->availableTasks(),
            'dailyLog' => null,
            'action' => $roleView === 'employee'
                ? route('employee.daily-logs.store')
                : route('supervisor.daily-logs.store'),
        ]);
    }

    private function ownShow(DailyLog $dailyLog, string $roleView)
    {
        abort_unless($dailyLog->user_id === Auth::id(), 403);

        return view('daily-logs.show', [
            'dailyLog' => $dailyLog->load(['user.department', 'task', 'reviews.reviewer']),
            'roleView' => $roleView,
            'canReview' => false,
        ]);
    }

    private function storeOwnLog(Request $request, string $roleView)
    {
        $validated = $request->validate([
            'log_date' => [
                'required',
                'date',
                'before_or_equal:today',
                Rule::unique('daily_logs', 'log_date')->where('user_id', Auth::id()),
            ],
            'task_id' => ['nullable', 'integer', Rule::in($this->availableTasks()->pluck('id')->all())],
            'title' => ['required', 'string', 'max:255'],
            'activities' => ['required', 'string', 'min:10', 'max:4000'],
            'task_progress' => ['nullable', 'string', 'max:3000'],
            'challenges' => ['nullable', 'string', 'max:3000'],
        ]);

        $log = DailyLog::create([
            ...$validated,
            'user_id' => Auth::id(),
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        $this->notifyReviewers($log);

        return redirect()
            ->route($roleView.'.daily-logs.show', $log)
            ->with('success', 'Daily work log submitted for review.');
    }

    private function reviewLog(Request $request, DailyLog $dailyLog, string $reviewerRole)
    {
        abort_unless($dailyLog->status === 'pending', 403);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $dailyLog->reviews()->create([
            'reviewed_by' => Auth::id(),
            'reviewer_role' => $reviewerRole,
            'status' => $validated['status'],
            'comment' => $validated['comment'] ?? null,
            'reviewed_at' => now(),
        ]);

        $dailyLog->update([
            'status' => $validated['status'] === 'approved' ? 'reviewed' : 'rejected',
        ]);

        Notification::create([
            'user_id' => $dailyLog->user_id,
            'type' => 'daily_log',
            'title' => 'Daily log reviewed',
            'message' => 'Your daily log for '.$dailyLog->log_date->format('M d, Y').' was '.$validated['status'].'.',
            'notifiable_type' => DailyLog::class,
            'notifiable_id' => $dailyLog->id,
            'action_url' => $dailyLog->user?->role === 'supervisor'
                ? route('supervisor.daily-logs.show', $dailyLog)
                : route('employee.daily-logs.show', $dailyLog),
            'icon' => 'clipboard-check',
            'color' => $validated['status'] === 'approved' ? 'green' : 'red',
        ]);

        return back()->with('success', 'Daily log review saved successfully.');
    }

    private function availableTasks()
    {
        return Task::query()
            ->where('assigned_to', Auth::id())
            ->whereIn('status', ['pending', 'in_progress'])
            ->latest()
            ->get();
    }

    private function notifyReviewers(DailyLog $log): void
    {
        $submitter = $log->user;
        $reviewers = $submitter?->role === 'supervisor'
            ? User::query()->where('role', 'hr')->get()
            : User::query()->whereKey($submitter?->supervisor_id)->get();

        foreach ($reviewers as $reviewer) {
            Notification::create([
                'user_id' => $reviewer->id,
                'type' => 'daily_log',
                'title' => 'Daily log requires review',
                'message' => $submitter->name.' submitted a daily work log for '.$log->log_date->format('M d, Y').'.',
                'notifiable_type' => DailyLog::class,
                'notifiable_id' => $log->id,
                'action_url' => $submitter->role === 'supervisor'
                    ? route('hr.daily-log-reviews.show', $log)
                    : route('supervisor.daily-log-reviews.show', $log),
                'icon' => 'book-open',
                'color' => 'blue',
            ]);
        }
    }
}

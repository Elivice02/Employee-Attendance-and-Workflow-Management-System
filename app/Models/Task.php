<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $fillable = [
        'assigned_by',
        'assigned_to',
        'title',
        'description',
        'due_date',
        'start_date',
        'end_date',
        'priority',
        'status',
        'scope',
        'completion_percentage',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the supervisor who assigned this task
     */
    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get the employee assigned to this task
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get all progress records for this task
     */
    public function progress(): HasMany
    {
        return $this->hasMany(TaskProgress::class)->orderBy('progress_date');
    }

    /**
     * Get latest progress records (for compatibility)
     */
    public function updates(): HasMany
    {
        return $this->hasMany(TaskUpdate::class)->latest();
    }

    /**
     * Get all progress records with latest first
     */
    public function progressReverse(): HasMany
    {
        return $this->hasMany(TaskProgress::class)->orderByDesc('progress_date');
    }

    /**
     * Check if task can be started
     */
    public function canStart(): bool
    {
        return $this->status === 'assigned' && is_null($this->started_at);
    }

    /**
     * Check if task can be completed
     */
    public function canComplete(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if task is pending supervisor review
     */
    public function isPendingReview(): bool
    {
        return $this->status === 'pending_review';
    }

    /**
     * Check if task needs revision
     */
    public function needsRevision(): bool
    {
        return $this->status === 'in_revision';
    }

    /**
     * Check if task is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Get the number of days until deadline
     */
    public function daysUntilDeadline(): int
    {
        $end = $this->end_date ?? $this->due_date;
        if (!$end) {
            return 0;
        }
        return now()->diffInDays($end, false);
    }

    /**
     * Check if task is overdue
     */
    public function isOverdue(): bool
    {
        $end = $this->end_date ?? $this->due_date;
        if (!$end) {
            return false;
        }
        return now() > $end && !$this->isCompleted();
    }

    /**
     * Get task duration in days
     */
    public function getDurationDays(): int
    {
        $start = $this->start_date ?? $this->created_at;
        $end = $this->end_date ?? $this->due_date;

        if (!$start || !$end) {
            return 0;
        }

        return $start->diffInDays($end);
    }

    /**
     * Calculate actual progress percentage
     */
    public function calculateProgressPercentage(): int
    {
        if ($this->progress()->count() === 0) {
            return 0;
        }

        return (int) $this->progressReverse()
            ->orderByDesc('id')
            ->value('completion_percentage');
    }

    /**
     * Update completion percentage from progress records
     */
    public function updateCompletionPercentage(): void
    {
        $percentage = $this->calculateProgressPercentage();
        $this->update(['completion_percentage' => $percentage]);
    }

    /**
     * Get task status badge color
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            'assigned' => 'secondary',
            'in_progress' => 'primary',
            'pending_review' => 'warning',
            'completed' => 'success',
            'in_revision' => 'danger',
            'archived' => 'dark',
            default => 'secondary',
        };
    }

    /**
     * Scope: Get tasks assigned by a user
     */
    public function scopeAssignedBy($query, $userId)
    {
        return $query->where('assigned_by', $userId);
    }

    /**
     * Scope: Get tasks assigned to a user
     */
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Scope: Get active tasks
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['assigned', 'in_progress', 'pending_review']);
    }

    /**
     * Scope: Get completed tasks
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: Get operational tasks
     */
    public function scopeOperational($query)
    {
        return $query->where('scope', 'operational');
    }

    /**
     * Scope: Get HR compliance tasks
     */
    public function scopeComplianceTask($query)
    {
        return $query->where('scope', 'hr_compliance');
    }
}

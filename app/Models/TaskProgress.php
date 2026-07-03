<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskProgress extends Model
{
    protected $table = 'task_progress';

    protected $fillable = [
        'task_id',
        'employee_id',
        'progress_date',
        'work_done',
        'completion_percentage',
        'challenges',
        'attachment_path',
        'supervisor_reviewed_at',
        'reviewed_by',
        'remarks',
    ];

    protected $casts = [
        'progress_date' => 'date',
        'supervisor_reviewed_at' => 'datetime',
    ];

    /**
     * Get the task this progress belongs to
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the employee who submitted this progress
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Get the supervisor who reviewed this progress
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Check if this progress record has been reviewed
     */
    public function isReviewed(): bool
    {
        return !is_null($this->supervisor_reviewed_at);
    }

    /**
     * Mark this progress as reviewed
     */
    public function markAsReviewed(User $supervisor, ?string $remarks = null): void
    {
        $this->update([
            'supervisor_reviewed_at' => now(),
            'reviewed_by' => $supervisor->id,
            'remarks' => $remarks,
        ]);
    }

    /**
     * Get day of week for display
     */
    public function getDayOfWeek(): string
    {
        return $this->progress_date->format('l'); // Monday, Tuesday, etc.
    }

    /**
     * Scope: Get unreviewed progress records
     */
    public function scopeUnreviewed($query)
    {
        return $query->whereNull('supervisor_reviewed_at');
    }

    /**
     * Scope: Get reviewed progress records
     */
    public function scopeReviewed($query)
    {
        return $query->whereNotNull('supervisor_reviewed_at');
    }

    /**
     * Scope: Get progress for a specific date
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('progress_date', $date);
    }

    /**
     * Scope: Get progress between dates
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('progress_date', [$startDate, $endDate]);
    }
}

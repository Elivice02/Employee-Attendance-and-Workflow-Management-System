<?php

namespace App\Policies;

use App\Models\TaskProgress;
use App\Models\User;

class TaskProgressPolicy
{
    /**
     * Determine if the user can view task progress
     */
    public function view(User $user, TaskProgress $progress): bool
    {
        // Employee can view their own progress
        if ($user->id === $progress->employee_id) {
            return true;
        }

        // Supervisor can view progress on tasks they assigned
        if ($user->role === 'supervisor' && $progress->task->assigned_by === $user->id) {
            return true;
        }

        // HR can view all progress on compliance tasks
        if ($user->role === 'hr' && $progress->task->scope === 'hr_compliance') {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can create progress
     */
    public function create(User $user, TaskProgress $progress): bool
    {
        // Only the assigned employee can create progress
        return $user->id === $progress->employee_id &&
               in_array($progress->task->status, ['in_progress', 'in_revision']);
    }

    /**
     * Determine if the user can update progress
     */
    public function update(User $user, TaskProgress $progress): bool
    {
        // Progress can only be updated before supervisor review
        if ($progress->isReviewed()) {
            return false;
        }

        // Employee can update their own unreviewed progress
        return $user->id === $progress->employee_id;
    }

    /**
     * Determine if the user can delete progress
     */
    public function delete(User $user, TaskProgress $progress): bool
    {
        // Progress records cannot be deleted (audit trail)
        return false;
    }

    /**
     * Determine if the user can review progress
     */
    public function review(User $user, TaskProgress $progress): bool
    {
        // Only the supervisor can review progress
        return $user->role === 'supervisor' &&
               $progress->task->assigned_by === $user->id &&
               !$progress->isReviewed();
    }
}

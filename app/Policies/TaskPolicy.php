<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine if the user can view the task
     */
    public function view(User $user, Task $task): bool
    {
        // HR can view all HR compliance tasks
        if ($user->role === 'hr' && $task->scope === 'hr_compliance') {
            return true;
        }

        // Supervisor can view tasks they created or are assigned to their employees
        if ($user->role === 'supervisor') {
            return $task->assigned_by === $user->id || 
                   ($task->assigned_to === $user->id) ||
                   ($this->isEmployeeOfSupervisor($user, $task->assigned_to));
        }

        // Employee can view tasks assigned to them
        if ($user->role === 'employee') {
            return $task->assigned_to === $user->id;
        }

        return false;
    }

    /**
     * Determine if the user can create a task
     */
    public function create(User $user): bool
    {
        // Only supervisors and HR can create tasks
        return in_array($user->role, ['supervisor', 'hr']);
    }

    /**
     * Determine if the user can update a task
     */
    public function update(User $user, Task $task): bool
    {
        // Cannot update completed tasks
        if ($task->isCompleted()) {
            return false;
        }

        // HR can update their compliance tasks (before employee starts)
        if ($user->role === 'hr' && $task->scope === 'hr_compliance' && $task->status === 'assigned') {
            return $task->assigned_by === $user->id;
        }

        // Supervisor can update operational tasks only before employee starts
        if ($user->role === 'supervisor' && $task->scope === 'operational') {
            return $task->assigned_by === $user->id && $task->status === 'assigned';
        }

        return false;
    }

    /**
     * Determine if the user can delete a task
     */
    public function delete(User $user, Task $task): bool
    {
        // Only the creator can delete, and only if not started
        return $task->assigned_by === $user->id && $task->status === 'assigned';
    }

    /**
     * Determine if the user can start a task
     */
    public function start(User $user, Task $task): bool
    {
        // Only the assigned employee can start their task
        return $user->role === 'employee' && 
               $task->assigned_to === $user->id && 
               $task->canStart();
    }

    /**
     * Determine if the user can submit progress
     */
    public function submitProgress(User $user, Task $task): bool
    {
        // Only the assigned employee can submit progress
        return $user->role === 'employee' && 
               $task->assigned_to === $user->id && 
               in_array($task->status, ['in_progress', 'in_revision']);
    }

    /**
     * Determine if the user can review a task
     */
    public function review(User $user, Task $task): bool
    {
        // Only the supervisor who created the task can review
        return $user->role === 'supervisor' && 
               $task->assigned_by === $user->id && 
               $task->status === 'pending_review';
    }

    /**
     * Determine if the user can approve a task
     */
    public function approve(User $user, Task $task): bool
    {
        return $this->review($user, $task);
    }

    /**
     * Determine if the user can reject a task
     */
    public function reject(User $user, Task $task): bool
    {
        return $this->review($user, $task);
    }

    /**
     * Determine if the user can view task progress
     */
    public function viewProgress(User $user, Task $task): bool
    {
        return $this->view($user, $task);
    }

    /**
     * Determine if the user can review progress records
     */
    public function reviewProgress(User $user, Task $task): bool
    {
        return $this->review($user, $task);
    }

    /**
     * Helper: Check if an employee belongs to a supervisor
     */
    private function isEmployeeOfSupervisor(User $supervisor, $employeeId): bool
    {
        return $supervisor->employees()
            ->where('id', $employeeId)
            ->exists();
    }
}

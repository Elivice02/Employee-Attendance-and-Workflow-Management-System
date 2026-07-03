<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskProgress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_start_sets_both_task_start_fields(): void
    {
        [$supervisor, $employee] = $this->users();
        $task = $this->task($supervisor, $employee);

        $this->actingAs($employee)
            ->post(route('employee.tasks.start', $task))
            ->assertRedirect(route('employee.tasks.show', $task));

        $task->refresh();

        $this->assertSame('in_progress', $task->status);
        $this->assertNotNull($task->start_date);
        $this->assertNotNull($task->started_at);
    }

    public function test_progress_update_cannot_use_a_progress_record_from_another_task(): void
    {
        [$supervisor, $employee] = $this->users();
        $task = $this->task($supervisor, $employee, ['status' => 'in_progress']);
        $otherTask = $this->task($supervisor, $employee, ['status' => 'in_progress']);
        $progress = TaskProgress::create([
            'task_id' => $otherTask->id,
            'employee_id' => $employee->id,
            'progress_date' => today(),
            'work_done' => 'Initial work',
            'completion_percentage' => 20,
        ]);

        $this->actingAs($employee)
            ->put(route('employee.tasks.progress.update', [$task, $progress]), [
                'work_done' => 'Tampered work',
                'completion_percentage' => 90,
            ])
            ->assertNotFound();

        $this->assertSame(20, $progress->refresh()->completion_percentage);
    }

    public function test_task_completion_uses_the_latest_progress_entry(): void
    {
        [$supervisor, $employee] = $this->users();
        $task = $this->task($supervisor, $employee, ['status' => 'in_progress']);

        TaskProgress::create([
            'task_id' => $task->id,
            'employee_id' => $employee->id,
            'progress_date' => today()->subDay(),
            'work_done' => 'First day',
            'completion_percentage' => 20,
        ]);
        TaskProgress::create([
            'task_id' => $task->id,
            'employee_id' => $employee->id,
            'progress_date' => today(),
            'work_done' => 'Second day',
            'completion_percentage' => 70,
        ]);

        $task->updateCompletionPercentage();

        $this->assertSame(70, $task->refresh()->completion_percentage);
    }

    private function users(): array
    {
        $supervisor = User::factory()->create(['role' => 'supervisor']);
        $employee = User::factory()->create([
            'role' => 'employee',
            'supervisor_id' => $supervisor->id,
        ]);

        return [$supervisor, $employee];
    }

    private function task(User $supervisor, User $employee, array $attributes = []): Task
    {
        return Task::create(array_merge([
            'assigned_by' => $supervisor->id,
            'assigned_to' => $employee->id,
            'title' => 'Workflow task',
            'description' => 'Task workflow test',
            'priority' => 'critical',
            'status' => 'assigned',
            'scope' => 'operational',
            'due_date' => today()->addWeek(),
        ], $attributes));
    }
}

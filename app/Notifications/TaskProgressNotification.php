<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\TaskProgress;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskProgressNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private Task $task, private TaskProgress $progress)
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'task_id' => $this->task->id,
            'progress_id' => $this->progress->id,
            'title' => 'Task Progress Update',
            'message' => "{$this->task->assignee->name} updated progress on '{$this->task->title}' to {$this->progress->completion_percentage}%",
            'type' => 'task_progress',
            'url' => route('supervisor.tasks.show', $this->task),
        ];
    }
}

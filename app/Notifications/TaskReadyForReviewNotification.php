<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskReadyForReviewNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private Task $task)
    {
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("Task Ready for Review: {$this->task->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line("The task '{$this->task->title}' is ready for your review.")
            ->line("{$this->task->assignee->name} has submitted all daily progress records.")
            ->line("Overall Progress: {$this->task->completion_percentage}%")
            ->action('Review Task', route('supervisor.tasks.show', $this->task))
            ->line('Please review the progress records and approve or request changes.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'task_id' => $this->task->id,
            'title' => 'Task Ready for Review',
            'message' => "'{$this->task->title}' is ready for your review. All progress records have been submitted.",
            'type' => 'task_review',
            'url' => route('supervisor.tasks.show', $this->task),
        ];
    }
}

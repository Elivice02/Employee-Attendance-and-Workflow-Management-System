<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private Task $task, private string $remarks)
    {
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("Task Revision Needed: {$this->task->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your task needs some revisions.")
            ->line("**{$this->task->title}**")
            ->line("Feedback:")
            ->line($this->remarks)
            ->action('Reopen Task', route('employee.tasks.show', $this->task))
            ->line('Please make the requested changes and resubmit your progress updates.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'task_id' => $this->task->id,
            'title' => 'Task Revision Needed',
            'message' => "Your task '{$this->task->title}' needs revision: {$this->remarks}",
            'type' => 'task_rejected',
            'url' => route('employee.tasks.show', $this->task),
        ];
    }
}

<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification implements ShouldQueue
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
            ->subject("New Task Assigned: {$this->task->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line("You have been assigned a new task:")
            ->line("**{$this->task->title}**")
            ->line($this->task->description)
            ->line("Deadline: {$this->task->end_date->format('F d, Y')}")
            ->action('View Task', route('employee.tasks.show', $this->task))
            ->line('Please start working on this task and submit daily progress updates.')
            ->salutation('Best regards,');
    }

    public function toDatabase($notifiable)
    {
        return [
            'task_id' => $this->task->id,
            'title' => "New Task Assigned: {$this->task->title}",
            'message' => "You have been assigned: {$this->task->title} (Due: {$this->task->end_date->format('M d')})",
            'type' => 'task_assigned',
            'url' => route('employee.tasks.show', $this->task),
        ];
    }
}

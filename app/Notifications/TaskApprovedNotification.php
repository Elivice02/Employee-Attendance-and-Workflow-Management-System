<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private Task $task, private ?string $remarks = null)
    {
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject("Task Approved: {$this->task->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Congratulations! Your task has been approved.")
            ->line("**{$this->task->title}**")
            ->line("Status: Completed ✓");

        if ($this->remarks) {
            $mail->line("Feedback: {$this->remarks}");
        }

        return $mail->salutation('Great work!');
    }

    public function toDatabase($notifiable)
    {
        $message = "Your task '{$this->task->title}' has been approved.";
        if ($this->remarks) {
            $message .= " Supervisor says: {$this->remarks}";
        }

        return [
            'task_id' => $this->task->id,
            'title' => 'Task Approved',
            'message' => $message,
            'type' => 'task_approved',
            'url' => route('employee.tasks.show', $this->task),
        ];
    }
}

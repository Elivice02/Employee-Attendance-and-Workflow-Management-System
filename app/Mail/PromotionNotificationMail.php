<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class PromotionNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $newRole;

    public function __construct(User $user, $newRole)
    {
        $this->user = $user;
        $this->newRole = $newRole;
    }

    public function build()
    {
        return $this
            ->subject('Promotion Notification')
            ->view('emails.promotion');
    }
}
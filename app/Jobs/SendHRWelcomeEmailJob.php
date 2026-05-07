<?php

namespace App\Jobs;

use App\Models\User;
use App\Mail\NewUserCredentialsMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendHRWelcomeEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $password
    ) {}

    public function handle(): void
    {
        Mail::to($this->user->email)->send(
            new NewUserCredentialsMail($this->user, $this->password)
        );
    }
}
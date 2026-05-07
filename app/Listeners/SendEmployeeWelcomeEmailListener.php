<?php

namespace App\Listeners;

use App\Events\EmployeeCreated;
use App\Jobs\SendHRWelcomeEmailJob;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEmployeeWelcomeEmailListener
{
    public function handle(EmployeeCreated $event): void
    {
        // push to queue (non-blocking)
        SendHRWelcomeEmailJob::dispatch(
            $event->user,
            $event->tempPassword
        );
    }
}

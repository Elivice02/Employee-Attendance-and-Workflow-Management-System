<?php

namespace App\Listeners;

use App\Events\HRCreated;
use App\Jobs\SendHRWelcomeEmailJob;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendHRWelcomeEmailListener
{
    public function handle(HRCreated $event): void
    {
        // push to queue (non-blocking)
        SendHRWelcomeEmailJob::dispatch(
            $event->user,
            $event->tempPassword
        );
    }
}
<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings.
     */
    protected $listen = [
        \App\Events\HRCreated::class => [
            \App\Listeners\SendHRWelcomeEmailListener::class,
        ],
        \App\Events\EmployeeCreated::class => [
            \App\Listeners\SendEmployeeWelcomeEmailListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be auto-discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false; // keep manual control (better for beginners)
    }
}
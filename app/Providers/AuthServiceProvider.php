<?php

namespace App\Providers;

use App\Models\Announcement;
use App\Models\Task;
use App\Models\TaskProgress;
use App\Policies\AnnouncementPolicy;
use App\Policies\TaskPolicy;
use App\Policies\TaskProgressPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Announcement::class => AnnouncementPolicy::class,
        Task::class => TaskPolicy::class,
        TaskProgress::class => TaskProgressPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}

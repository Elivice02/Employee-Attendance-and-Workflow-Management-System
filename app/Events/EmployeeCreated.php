<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

class EmployeeCreated
{
    use Dispatchable;

    public function __construct(
        public User $user,
        public string $tempPassword
    ) {}
}

<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;

class AnnouncementPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['hr', 'admin']);
    }

    public function view(User $user, Announcement $announcement): bool
    {
        return in_array($user->role, ['hr', 'admin'])
            || $announcement->recipients()->whereKey($user->id)->exists();
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['hr', 'admin']);
    }

    public function update(User $user, Announcement $announcement): bool
    {
        return $user->role === 'admin'
            || ($user->role === 'hr' && $user->id === $announcement->created_by);
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        return $user->role === 'admin';
    }
}

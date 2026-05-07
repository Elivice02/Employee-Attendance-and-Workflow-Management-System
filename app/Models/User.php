<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $fillable = [
        'created_by',
        'name',
        'email',
        'password',
        'password_changed_at',
        'gender',
        'date_of_birth',
        'phone',
        'profile_picture',
        'role',
        'department_id',
        'supervisor_id',
        'must_change_password'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password_changed_at' => 'datetime',
    ];

    /**
     * Check if password is expired (3 months)
     */
    public function isPasswordExpired(): bool
    {
        if (!$this->password_changed_at) {
            return false;
        }

        return now() > $this->password_changed_at->addMonths(3);
    }

    /**
     * Check if password expires soon (within 7 days)
     */
    public function isPasswordExpiringSoon(): bool
    {
        if (!$this->password_changed_at) {
            return false;
        }

        $expiryDate = $this->password_changed_at->addMonths(3);
        return now() > $expiryDate->subDays(7) && now() <= $expiryDate;
    }

    /**
     * Get days until password expires
     */
    public function getDaysUntilPasswordExpires(): int
    {
        if (!$this->password_changed_at) {
            return 0;
        }

        $expiryDate = $this->password_changed_at->addMonths(3);
        return max(0, now()->diffInDays($expiryDate, false));
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    

    // supervisor (for employees)
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    // employees under a supervisor
    public function employees()
    {
        return $this->hasMany(User::class, 'supervisor_id');
    }

    // who created this user (audit)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

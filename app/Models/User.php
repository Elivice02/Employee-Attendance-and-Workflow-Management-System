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

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function attendanceNotifications()
    {
        return $this->hasMany(AttendanceNotification::class, 'recipient_id');
    }

    public function appNotifications()
    {
        return $this->hasMany(Notification::class);
    }

    // who created this user (audit)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all leave requests submitted by this employee
     */
    public function leaveRequests()
    {
        return $this->hasMany(Leave::class, 'user_id');
    }

    /**
     * Get all leave requests where this user is the supervisor reviewer
     */
    public function supervisorLeaveReviews()
    {
        return $this->hasMany(Leave::class, 'supervisor_id');
    }

    /**
     * Get all leave requests where this user is the HR reviewer
     */
    public function hrLeaveReviews()
    {
        return $this->hasMany(Leave::class, 'hr_id');
    }
}

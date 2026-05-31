<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceNotification extends Model
{
    protected $fillable = [
        'attendance_id',
        'recipient_id',
        'type',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}

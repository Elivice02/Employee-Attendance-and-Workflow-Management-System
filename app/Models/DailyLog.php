<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyLog extends Model
{
    protected $fillable = [
        'user_id',
        'task_id',
        'log_date',
        'title',
        'activities',
        'task_progress',
        'challenges',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'log_date' => 'date',
        'submitted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function reviews()
    {
        return $this->hasMany(DailyLogReview::class)->latest();
    }
}

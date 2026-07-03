<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyLogReview extends Model
{
    protected $fillable = [
        'daily_log_id',
        'reviewed_by',
        'reviewer_role',
        'status',
        'comment',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function dailyLog()
    {
        return $this->belongsTo(DailyLog::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}

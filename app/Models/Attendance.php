<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'attendance_date',
        'check_in_at',
        'check_out_at',
        'check_in_ip',
        'check_out_ip',
        'status',
        'late_explanation',
        'late_opening_paragraph',
        'late_closing_paragraph',
        'late_signature_name',
        'late_evidence_path',
        'late_letter_reference',
        'late_letter_draft_path',
        'late_letter_final_path',
        'late_submitted_at',
        'late_review_status',
        'late_reviewed_by',
        'late_reviewed_at',
        'late_review_note',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
        'late_submitted_at' => 'datetime',
        'late_reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'late_reviewed_by');
    }

    public function notifications()
    {
        return $this->hasMany(AttendanceNotification::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $table = 'leave_requests';

    protected $fillable = [
        'ref_no',
        'user_id',
        'leave_type',
        'leave_type_number',
        'leave_type_name',
        'leave_type_standing_order',
        'start_date',
        'end_date',
        'total_days',
        'reason',
        'attachment_path',
        'status',
        'current_stage',
        'section_a',
        'section_b',
        'section_c',
        'section_d',
        'submitted_at',
        'supervisor_id',
        'supervisor_comment',
        'supervisor_reviewed_at',
        'hr_id',
        'hr_comment',
        'hr_reviewed_at',
        'pdf_path',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'section_a' => 'array',
        'section_b' => 'array',
        'section_c' => 'array',
        'section_d' => 'array',
        'submitted_at' => 'datetime',
        'supervisor_reviewed_at' => 'datetime',
        'hr_reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the employee who requested the leave
     */
    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the supervisor who reviewed this leave
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Get the HR manager who gave final approval
     */
    public function hrReviewer()
    {
        return $this->belongsTo(User::class, 'hr_id');
    }

    /**
     * Check if leave is pending supervisor approval
     */
    public function isPendingSupervisor(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if leave is pending HR approval
     */
    public function isPendingHR(): bool
    {
        return $this->status === 'supervisor_approved';
    }

    /**
     * Check if leave is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'hr_approved';
    }

    /**
     * Check if leave is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}

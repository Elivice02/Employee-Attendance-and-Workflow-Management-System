<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    protected $fillable = [
        'company_name',
        'company_address',
        'work_start_time',
        'work_end_time',
        'late_grace_minutes',
        'allowed_networks',
        'late_evidence_required',
        'password_expiry_days',
        'default_annual_leave_days',
    ];

    protected $casts = [
        'late_evidence_required' => 'boolean',
    ];

    public static function current(): self
    {
        return self::query()->firstOrCreate([], [
            'company_name' => config('attendance.company_name', 'Mzumbe Company Ltd'),
            'company_address' => config('attendance.company_address', 'P.O. Box 01, Morogoro'),
            'work_start_time' => config('attendance.work_start_time', '08:00:00'),
            'work_end_time' => config('attendance.work_end_time', '17:00:00'),
            'late_grace_minutes' => config('attendance.late_grace_minutes', 0),
            'allowed_networks' => config('attendance.allowed_networks'),
            'late_evidence_required' => true,
            'password_expiry_days' => 90,
            'default_annual_leave_days' => 20,
        ]);
    }

    public function networkList(): array
    {
        if (! $this->allowed_networks) {
            return [];
        }

        return collect(preg_split('/[\r\n,]+/', $this->allowed_networks))
            ->map(fn ($network) => trim($network))
            ->filter()
            ->values()
            ->all();
    }
}

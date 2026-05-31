<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    protected $fillable = [
        'work_start_time',
        'late_grace_minutes',
        'allowed_networks',
        'late_evidence_required',
    ];

    protected $casts = [
        'late_evidence_required' => 'boolean',
    ];

    public static function current(): self
    {
        return self::query()->firstOrCreate([], [
            'work_start_time' => config('attendance.work_start_time', '09:00:00'),
            'late_grace_minutes' => config('attendance.late_grace_minutes', 0),
            'allowed_networks' => config('attendance.allowed_networks'),
            'late_evidence_required' => true,
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

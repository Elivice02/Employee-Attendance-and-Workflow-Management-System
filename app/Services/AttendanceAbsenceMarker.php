<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\Leave;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class AttendanceAbsenceMarker
{
    public function markEligibleDatesThisMonth(): array
    {
        $settings = AttendanceSetting::current();
        $date = now()->copy()->startOfMonth();
        $lastEligibleDate = $this->lastEligibleDate($settings);
        $created = [
            'absent' => 0,
            'on_leave' => 0,
        ];

        while ($date->lte($lastEligibleDate)) {
            $result = $this->markDate($date);
            $created['absent'] += $result['absent'];
            $created['on_leave'] += $result['on_leave'];
            $date->addDay();
        }

        return $created;
    }

    public function markDate(CarbonInterface $date): array
    {
        $attendanceDate = Carbon::parse($date)->startOfDay();

        if ($attendanceDate->isFuture() || ! $attendanceDate->isWeekday()) {
            return [
                'absent' => 0,
                'on_leave' => 0,
            ];
        }

        $created = [
            'absent' => 0,
            'on_leave' => 0,
        ];

        User::query()
            ->whereIn('role', ['employee', 'hr', 'supervisor'])
            ->select('id')
            ->orderBy('id')
            ->chunkById(100, function ($users) use ($attendanceDate, &$created) {
                foreach ($users as $user) {
                    $status = $this->hasApprovedLeaveOnDate($user->id, $attendanceDate)
                        ? 'on_leave'
                        : 'absent';

                    $attendance = Attendance::query()->firstOrCreate(
                        [
                            'user_id' => $user->id,
                            'attendance_date' => $attendanceDate->toDateString(),
                        ],
                        [
                            'status' => $status,
                        ]
                    );

                    if ($attendance->wasRecentlyCreated) {
                        $created[$status]++;
                    }
                }
            });

        return $created;
    }

    private function lastEligibleDate(AttendanceSetting $settings): Carbon
    {
        $todayWorkEnd = today()->setTimeFromTimeString($settings->work_end_time);

        if (now()->gte($todayWorkEnd)) {
            return today();
        }

        return today()->subDay();
    }

    private function hasApprovedLeaveOnDate(int $userId, CarbonInterface $date): bool
    {
        return Leave::query()
            ->where('user_id', $userId)
            ->where('status', 'hr_approved')
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->exists();
    }
}

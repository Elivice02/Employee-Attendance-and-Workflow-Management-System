<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\AttendanceAbsenceMarker;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('attendance:mark-absent {date? : Date to mark in YYYY-MM-DD format}', function () {
    $date = $this->argument('date');
    $marker = app(AttendanceAbsenceMarker::class);

    $result = $date
        ? $marker->markDate(Carbon::parse($date))
        : $marker->markEligibleDatesThisMonth();

    $this->info("Created {$result['absent']} absent attendance record(s).");
    $this->info("Created {$result['on_leave']} approved leave attendance record(s).");
})->purpose('Create absent attendance records for attendance users who did not check in');

Schedule::command('attendance:mark-absent')
    ->hourly()
    ->withoutOverlapping();

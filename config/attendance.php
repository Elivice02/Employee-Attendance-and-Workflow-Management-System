<?php

return [
    'company_name' => env('ATTENDANCE_COMPANY_NAME', 'Mzumbe Company Ltd'),
    'company_address' => env('ATTENDANCE_COMPANY_ADDRESS', 'P.O. Box 01, Morogoro'),
    'work_start_time' => env('ATTENDANCE_WORK_START_TIME', '08:00:00'),
    'late_grace_minutes' => (int) env('ATTENDANCE_LATE_GRACE_MINUTES', 0),
    'allowed_networks' => env('ATTENDANCE_ALLOWED_NETWORKS', '127.0.0.1,::1'),
];

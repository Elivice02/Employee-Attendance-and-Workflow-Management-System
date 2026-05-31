<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class HRDashboardController extends Controller
{
    public function index()
    {
        $attendanceData = AttendanceController::dashboardDataFor(Auth::user());
        $lateNotifications = AttendanceController::notificationsFor(Auth::user());

        return view('hr.dashboard', [
            ...$attendanceData,
            'lateNotifications' => $lateNotifications,
            'totalEmployees' => User::whereIn('role', ['employee', 'supervisor', 'hr'])->count(),
            'presentToday' => Attendance::whereDate('attendance_date', today())->whereNotNull('check_in_at')->count(),
            'latePending' => Attendance::whereDate('attendance_date', today())->where('status', 'late_pending_review')->count(),
        ]);
    }
}

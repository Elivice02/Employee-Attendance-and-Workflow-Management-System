<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SupervisorController extends Controller
{
    public function dashboard()
    {
        $supervisor = auth()->user();

        $employees = $supervisor->employees()
            ->where('role', 'employee')
            ->get();
        $attendanceData = AttendanceController::dashboardDataFor($supervisor);
        $lateNotifications = AttendanceController::notificationsFor($supervisor);

        return view('supervisor.dashboard', [
            'employees' => $employees,
            'lateNotifications' => $lateNotifications,
            ...$attendanceData,
        ]);
    }

    public function index()
    {
        $totalEmployees = Employee::count();

        $activeTasks = Task::where('status', 'active')->count();

        $pendingApprovals = Task::where('status', 'pending')->count();

        $alerts = Alert::where('is_read', false)->count();

        $employees = Employee::latest()->take(10)->get();

        $recentAlerts = Alert::latest()->take(5)->get();

        return view('supervisor.dashboard', compact(
            'totalEmployees',
            'activeTasks',
            'pendingApprovals',
            'alerts',
            'employees',
            'recentAlerts'
        ));
    }
}

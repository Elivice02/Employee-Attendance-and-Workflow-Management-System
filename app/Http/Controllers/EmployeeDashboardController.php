<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Leave;
use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\DailyLog;
use App\Models\Task;
use App\Rules\TanzaniaPhoneNumber;
use App\Support\TanzaniaPhoneNumber as PhoneNumber;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EmployeeDashboardController extends Controller
{
    /**
     * Show the employee dashboard
     */
    public function index()
    {
        $user = Auth::user();

        $expectedWorkingDays = $this->workingDaysElapsedThisMonth($user);

        $attendedDays = Attendance::query()
            ->where('user_id', $user->id)
            ->whereNotNull('check_in_at')
            ->whereMonth('attendance_date', now()->month)
            ->whereYear('attendance_date', now()->year)
            ->distinct('attendance_date')
            ->count('attendance_date');

        $attendanceRate = $expectedWorkingDays > 0 ? min(100, round(($attendedDays / $expectedWorkingDays) * 100)) : 0;

        // Count pending leave requests
        $pendingRequests = Leave::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'supervisor_approved'])
            ->count();

        $totalLeaves = AttendanceSetting::current()->default_annual_leave_days;

        // Calculate used annual leaves (approved only)
        $usedLeaves = Leave::query()
            ->where('user_id', $user->id)
            ->where('leave_type', 'annual')
            ->where('status', 'hr_approved')
            ->whereYear('start_date', now()->year)
            ->sum('total_days');

        $stats = [
            'attendance_rate' => $attendanceRate,
            'attended_days' => $attendedDays,
            'expected_working_days' => $expectedWorkingDays,
            'pending_requests' => $pendingRequests,
            'total_leaves' => $totalLeaves,
            'used_leaves' => $usedLeaves,
            'remaining_leaves' => max(0, $totalLeaves - $usedLeaves),
            'over_limit_leaves' => max(0, $usedLeaves - $totalLeaves),
        ];

        $attendanceData = AttendanceController::dashboardDataFor($user);
        $lateNotifications = AttendanceController::notificationsFor($user);
        $recentActivities = $this->recentActivities($user);

        // Get announcement data
        $unreadAnnouncements = $user->unreadAnnouncements()
            ->take(3)
            ->get();
        $unreadAnnouncementCount = $user->getUnreadAnnouncementCount();

        return view('employee.dashboard.index', [
            'user' => $user,
            'stats' => $stats,
            'lateNotifications' => $lateNotifications,
            'recentActivities' => $recentActivities,
            'unreadAnnouncements' => $unreadAnnouncements,
            'unreadAnnouncementCount' => $unreadAnnouncementCount,
            ...$attendanceData,
        ]);
    }

    private function workingDaysElapsedThisMonth(User $user): int
    {
        $date = now()->copy()->startOfMonth();
        $today = now()->copy()->startOfDay();
        $workingDays = 0;

        while ($date->lte($today)) {
            if ($date->isWeekday() && ! $this->hasApprovedLeaveOnDate($user, $date)) {
                $workingDays++;
            }

            $date->addDay();
        }

        return $workingDays;
    }

    private function hasApprovedLeaveOnDate(User $user, $date): bool
    {
        return Leave::query()
            ->where('user_id', $user->id)
            ->where('status', 'hr_approved')
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->exists();
    }

    private function recentActivities(User $user)
    {
        $attendanceActivities = Attendance::query()
            ->where('user_id', $user->id)
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(fn (Attendance $attendance) => [
                'label' => 'Attendance marked as ' . str_replace('_', ' ', $attendance->status)
                    . ' for ' . $attendance->attendance_date->format('M d, Y'),
                'time' => $attendance->updated_at,
            ]);

        $leaveActivities = Leave::query()
            ->where('user_id', $user->id)
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(fn (Leave $leave) => [
                'label' => ucfirst(str_replace('_', ' ', $leave->leave_type)) . ' leave request is '
                    . str_replace('_', ' ', $leave->status),
                'time' => $leave->updated_at,
            ]);

        $taskActivities = Task::query()
            ->where('assigned_to', $user->id)
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(fn (Task $task) => [
                'label' => 'Task "' . $task->title . '" is ' . str_replace('_', ' ', $task->status),
                'time' => $task->updated_at,
            ]);

        $dailyLogActivities = DailyLog::query()
            ->where('user_id', $user->id)
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(fn (DailyLog $dailyLog) => [
                'label' => 'Daily log "' . $dailyLog->title . '" is ' . str_replace('_', ' ', $dailyLog->status),
                'time' => $dailyLog->updated_at,
            ]);

        return collect()
            ->merge($attendanceActivities)
            ->merge($leaveActivities)
            ->merge($taskActivities)
            ->merge($dailyLogActivities)
            ->sortByDesc('time')
            ->take(6)
            ->values();
    }

    /**
     * Show employee profile
     */
    public function profile()
    {
        $user = Auth::user();
        return view('employee.dashboard.profile', compact('user'));
    }

    /**
     * Update employee profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'date_of_birth' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:20', new TanzaniaPhoneNumber],
            'profile_picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $validated['phone'] = PhoneNumber::normalize($validated['phone'] ?? null);

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $validated['profile_picture'] = $request->file('profile_picture')->store('profiles', 'public');
        }

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully');
    }

    /**
     * Show attendance records
     */
    public function attendance()
    {
        return redirect()->route('employee.attendance');
    }

    /**
     * Show leave requests from database
     */
    public function leaveRequests()
    {
        $user = Auth::user();
        // Fetch from database
        $leaves = $user->leaveRequests()
            ->orderByDesc('created_at')
            ->get();

        return view('employee.dashboard.leave-requests', compact('leaves'));
    }

    /**
     * Show form to request leave
     */
    public function requestLeaveForm()
    {
        return view('employee.dashboard.request-leave');
    }

    /**
     * Store a new leave request to database
     */
    public function requestLeave(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'leave_type' => 'required|in:annual,sick,maternity,paternity,emergency,unpaid',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|min:10|max:1000',
            'attachment_path' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png,doc,docx',
        ]);

        // Calculate total days
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $endDate = \Carbon\Carbon::parse($validated['end_date']);
        $totalDays = $startDate->diffInDays($endDate) + 1;

        // Handle file upload if provided
        $attachmentPath = null;
        if ($request->hasFile('attachment_path')) {
            $attachmentPath = $request->file('attachment_path')->store('leave-attachments', 'public');
        }

        // Get supervisor for this employee
        $supervisor = $user->supervisor;

        // Create leave request
        Leave::create([
            'user_id' => $user->id,
            'leave_type' => $validated['leave_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_days' => $totalDays,
            'reason' => $validated['reason'],
            'attachment_path' => $attachmentPath,
            'status' => 'pending',
            'supervisor_id' => $supervisor?->id,
        ]);

        return redirect()
            ->route('employee.leave.index')
            ->with('success', 'Leave request submitted successfully. Your supervisor will review it.');
    }

    /**
     * Show payroll information (Coming Soon)
     */
    public function payroll()
    {
        return view('employee.dashboard.payroll-coming-soon');
    }

    /**
     * Show password change form
     */
    public function changePasswordForm()
    {
        return view('employee.dashboard.change-password');
    }

    /**
     * Update password
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        Auth::user()->update([
            'password' => bcrypt($validated['new_password']),
            'must_change_password' => false,
        ]);

        return back()->with('success', 'Password changed successfully');
    }
}


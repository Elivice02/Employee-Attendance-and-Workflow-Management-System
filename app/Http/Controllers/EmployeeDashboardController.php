<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Leave;
use App\Models\Attendance;
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

        // Calculate real attendance rate for current month
        $totalWorkingDays = Attendance::query()
            ->where('user_id', $user->id)
            ->whereMonth('attendance_date', now()->month)
            ->whereYear('attendance_date', now()->year)
            ->count();

        $presentDays = Attendance::query()
            ->where('user_id', $user->id)
            ->where('status', 'present')
            ->whereMonth('attendance_date', now()->month)
            ->whereYear('attendance_date', now()->year)
            ->count();

        $attendanceRate = $totalWorkingDays > 0 ? round(($presentDays / $totalWorkingDays) * 100) : 0;

        // Count pending leave requests
        $pendingRequests = Leave::query()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        // Default leave allocation (20 days - can be configured in system settings later)
        $totalLeaves = 20;

        // Calculate used leaves (approved only)
        $usedLeaves = Leave::query()
            ->where('user_id', $user->id)
            ->where('status', 'hr_approved')
            ->whereYear('start_date', now()->year)
            ->sum('total_days');

        $stats = [
            'attendance_rate' => $attendanceRate,
            'pending_requests' => $pendingRequests,
            'total_leaves' => $totalLeaves,
            'used_leaves' => $usedLeaves,
        ];

        $attendanceData = AttendanceController::dashboardDataFor($user);
        $lateNotifications = AttendanceController::notificationsFor($user);

        return view('employee.dashboard.index', [
            'user' => $user,
            'stats' => $stats,
            'lateNotifications' => $lateNotifications,
            ...$attendanceData,
        ]);
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
            'phone' => ['nullable', 'string', 'max:20'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

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


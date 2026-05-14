<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
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
        $stats = [
            'attendance_rate' => rand(80, 99), // Mock data
            'pending_requests' => rand(0, 3),
            'total_leaves' => 20,
            'used_leaves' => rand(5, 15),
        ];

        return view('employee.dashboard.index', compact('user', 'stats'));
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
        $user = Auth::user();
        // Mock attendance data
        $records = [
            ['date' => now()->subDays(1), 'status' => 'Present', 'check_in' => '09:00', 'check_out' => '17:30'],
            ['date' => now()->subDays(2), 'status' => 'Present', 'check_in' => '09:15', 'check_out' => '17:45'],
            ['date' => now()->subDays(3), 'status' => 'Absent', 'check_in' => '-', 'check_out' => '-'],
            ['date' => now()->subDays(4), 'status' => 'Present', 'check_in' => '09:05', 'check_out' => '17:00'],
            ['date' => now()->subDays(5), 'status' => 'Leave', 'check_in' => '-', 'check_out' => '-'],
        ];

        return view('employee.dashboard.attendance', compact('records'));
    }

    /**
     * Show leave requests
     */
    public function leaveRequests()
    {
        $user = Auth::user();
        // Mock leave data
        $leaves = [
            ['type' => 'Annual Leave', 'from' => now()->addDays(10), 'to' => now()->addDays(12), 'status' => 'Pending', 'days' => 3],
            ['type' => 'Sick Leave', 'from' => now()->subDays(5), 'to' => now()->subDays(5), 'status' => 'Approved', 'days' => 1],
            ['type' => 'Casual Leave', 'from' => now()->subDays(15), 'to' => now()->subDays(15), 'status' => 'Rejected', 'days' => 1],
        ];

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
     * Store a new leave request
     */
    public function requestLeave(Request $request)
    {
        $validated = $request->validate([
            'leave_type' => 'required|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'reason' => 'required|string|max:500',
        ]);

        // Store leave request (would normally save to database)
        return back()->with('success', 'Leave request submitted successfully');
    }

    /**
     * Show payroll information
     */
    public function payroll()
    {
        $user = Auth::user();
        // Mock payroll data
        $salary_info = [
            'base_salary' => 50000,
            'allowances' => 5000,
            'deductions' => 2000,
            'net_salary' => 53000,
        ];

        $payslips = [
            ['month' => 'April 2026', 'amount' => 53000, 'status' => 'Paid'],
            ['month' => 'March 2026', 'amount' => 53000, 'status' => 'Paid'],
            ['month' => 'February 2026', 'amount' => 53000, 'status' => 'Paid'],
        ];

        return view('employee.dashboard.payroll', compact('salary_info', 'payslips'));
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

@extends('layouts.employee')

@section('title', 'Employee Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Stats Section -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <!-- Attendance Rate Card -->
        <div class="bg-blue-500 text-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">Attendance Rate</h3>
            <p class="text-4xl font-bold">{{ $stats['attendance_rate'] }}%</p>
            <p class="text-sm mt-2">Current Month</p>
        </div>

        <!-- Pending Requests Card -->
        <div class="bg-yellow-500 text-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">Pending Requests</h3>
            <p class="text-4xl font-bold">{{ $stats['pending_requests'] }}</p>
            <p class="text-sm mt-2">Awaiting Approval</p>
        </div>

        <!-- Leave Balance Card -->
        <div class="bg-green-500 text-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">Leave Balance</h3>
            <p class="text-4xl font-bold">{{ $stats['total_leaves'] - $stats['used_leaves'] }}</p>
            <p class="text-sm mt-2">Days Remaining</p>
        </div>

        <!-- Total Leaves Card -->
        <div class="bg-purple-500 text-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">Total Leaves</h3>
            <p class="text-4xl font-bold">{{ $stats['used_leaves'] }}/{{ $stats['total_leaves'] }}</p>
            <p class="text-sm mt-2">Days Used</p>
        </div>
    </div>

    <!-- Quick Links Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <a href="{{ route('employee.profile') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
            <h3 class="text-xl font-semibold mb-2">👤 My Profile</h3>
            <p class="text-gray-600">View and update your profile information</p>
        </a>

        <a href="{{ route('employee.attendance') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
            <h3 class="text-xl font-semibold mb-2">📅 Attendance</h3>
            <p class="text-gray-600">Check your attendance records</p>
        </a>

        <a href="{{ route('employee.leave.index') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
            <h3 class="text-xl font-semibold mb-2">📋 Leave Requests</h3>
            <p class="text-gray-600">View and request leaves</p>
        </a>

        <a href="{{ route('employee.payroll') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
            <h3 class="text-xl font-semibold mb-2">💰 Payroll</h3>
            <p class="text-gray-600">View your salary and payslips</p>
        </a>

        <a href="{{ route('employee.password.form') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
            <h3 class="text-xl font-semibold mb-2">🔐 Change Password</h3>
            <p class="text-gray-600">Update your password securely</p>
        </a>

        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="bg-red-500 text-white p-6 rounded-lg shadow hover:shadow-lg transition">
            <h3 class="text-xl font-semibold mb-2">🚪 Logout</h3>
            <p>Sign out from your account</p>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>

    <!-- Recent Activity Section -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-2xl font-bold mb-4">Recent Activity</h2>
        <p class="text-gray-600">No recent activities</p>
    </div>
</div>
@endsection

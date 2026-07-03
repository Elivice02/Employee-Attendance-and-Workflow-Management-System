@extends('layouts.employee')

@section('title', 'Employee Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    @include('attendance._widget')

    @include('attendance._notifications', ['attendanceReviewUrl' => route('employee.attendance')])

    <!-- Stats Section -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <!-- Attendance Rate Card -->
        <div class="bg-blue-500 text-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">Attendance Rate</h3>
            <p class="text-4xl font-bold">{{ $stats['attendance_rate'] }}%</p>
            <p class="text-sm mt-2">{{ $stats['attended_days'] }} of {{ $stats['expected_working_days'] }} workdays checked in</p>
        </div>

        <!-- Pending Requests Card -->
        <div class="bg-yellow-500 text-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">Pending Leave Requests</h3>
            <p class="text-4xl font-bold">{{ $stats['pending_requests'] }}</p>
            <p class="text-sm mt-2">Awaiting Approval</p>
        </div>

        <!-- Leave Balance Card -->
        <div class="bg-green-500 text-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">Annual Leave Remaining</h3>
            <p class="text-4xl font-bold">{{ $stats['remaining_leaves'] }}</p>
            <p class="text-sm mt-2">
                @if ($stats['over_limit_leaves'] > 0)
                    {{ $stats['over_limit_leaves'] }} days over annual limit
                @else
                    Days available this year
                @endif
            </p>
        </div>

        <!-- Total Leaves Card -->
        <div class="bg-purple-500 text-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">Annual Leave Used</h3>
            <p class="text-4xl font-bold">{{ $stats['used_leaves'] }}</p>
            <p class="text-sm mt-2">Allowed: {{ $stats['total_leaves'] }} days</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold">Announcements</h2>
            <a href="{{ route('announcements.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">
                View all
            </a>
        </div>

        @if ($unreadAnnouncements->isEmpty())
            <p class="text-gray-600">No unread announcements</p>
        @else
            <div class="space-y-3">
                @foreach ($unreadAnnouncements as $announcement)
                    <a href="{{ route('announcements.show', $announcement) }}" class="block rounded-lg border border-blue-100 bg-blue-50 p-4 hover:bg-blue-100 transition">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $announcement->title }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ \Illuminate\Support\Str::limit($announcement->message, 120) }}</p>
                            </div>
                            <span class="shrink-0 text-xs text-blue-700">
                                {{ $announcement->published_at?->diffForHumans() }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
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

        <a href="{{ route('announcements.index') }}" class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
            <h3 class="text-xl font-semibold mb-2">Announcements</h3>
            <p class="text-gray-600">{{ $unreadAnnouncementCount }} unread announcement{{ $unreadAnnouncementCount === 1 ? '' : 's' }}</p>
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
        @if ($recentActivities->isEmpty())
            <p class="text-gray-600">No recent activities</p>
        @else
            <ul class="space-y-3 text-sm text-gray-600">
                @foreach ($recentActivities as $activity)
                    <li class="flex items-start justify-between gap-4">
                        <span>{{ $activity['label'] }}</span>
                        <span class="shrink-0 text-xs text-gray-400">{{ $activity['time']?->diffForHumans() }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection

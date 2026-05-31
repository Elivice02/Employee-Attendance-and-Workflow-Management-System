@extends('layouts.hr')

@section('title', 'HR Dashboard')

@section('content')

<div class="space-y-8">

    @include('attendance._widget')

    @include('attendance._notifications', ['attendanceReviewUrl' => route('hr.attendance.index')])

    <!-- Quick Actions -->
    <div class="flex gap-3 mb-6">
        <a href="{{ route('hr.employees.create') }}"
           class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2 rounded-lg shadow">
            + Add Employee
        </a>

        <a href="{{ route('hr.employees.index') }}"
           class="bg-white border border-gray-300 hover:bg-gray-100 px-5 py-2 rounded-lg shadow">
            View Employees
        </a>
    </div>


    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        <div class="bg-white p-5 rounded-xl shadow border-l-4 border-blue-500">
            <p class="text-sm text-gray-500">Total Employees</p>
            <h2 class="text-3xl font-bold text-gray-800 mt-2">{{ $totalEmployees }}</h2>
        </div>

        <div class="bg-white p-5 rounded-xl shadow border-l-4 border-green-500">
            <p class="text-sm text-gray-500">Present Today</p>
            <h2 class="text-3xl font-bold text-green-600 mt-2">{{ $presentToday }}</h2>
        </div>

        <div class="bg-white p-5 rounded-xl shadow border-l-4 border-red-500">
            <p class="text-sm text-gray-500">Late Pending Review</p>
            <h2 class="text-3xl font-bold text-red-600 mt-2">{{ $latePending }}</h2>
        </div>

        <div class="bg-white p-5 rounded-xl shadow border-l-4 border-purple-500">
            <p class="text-sm text-gray-500">Assigned Departments</p>
            <h2 class="text-3xl font-bold text-gray-800 mt-2">6</h2>
        </div>

    </div>


    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <a href="{{ route('hr.employees.index') }}"
           class="bg-white p-5 rounded-xl shadow hover:shadow-lg transition">
            <h3 class="font-semibold text-lg text-gray-800">Employee Management</h3>
            <p class="text-gray-500 mt-2 text-sm">
                View, edit, promote, or deactivate employees.
            </p>
        </a>

        <a href="{{ route('hr.attendance.index') }}"
           class="bg-white p-5 rounded-xl shadow hover:shadow-lg transition">
            <h3 class="font-semibold text-lg text-gray-800">Attendance</h3>
            <p class="text-gray-500 mt-2 text-sm">
                Track employee attendance and daily records.
            </p>
        </a>

        <a href="#"
           class="bg-white p-5 rounded-xl shadow hover:shadow-lg transition">
            <h3 class="font-semibold text-lg text-gray-800">Staff Assignment</h3>
            <p class="text-gray-500 mt-2 text-sm">
                Assign employees and supervisors to existing departments.
            </p>
        </a>

    </div>


    <!-- Recent Activity -->
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
            Recent Activity
        </h2>

        <ul class="space-y-3 text-sm text-gray-600">
            <li>✅ John Doe promoted to Supervisor</li>
            <li>➕ New employee Sarah added to IT Department</li>
            <li>📅 Attendance marked for today</li>
            <li>🏢 Finance department updated</li>
        </ul>
    </div>

</div>

@endsection

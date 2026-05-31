@extends('layouts.supervisor')

@section('title', 'Supervisor Dashboard')

@section('content')

    @include('attendance._widget')

    @include('attendance._notifications', ['attendanceReviewUrl' => route('supervisor.attendance.index')])

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-gray-500 text-sm">Employees</h2>
            <p class="text-3xl font-bold mt-2">{{ $employees->count() }}</p>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-gray-500 text-sm">Tasks</h2>
            <p class="text-3xl font-bold mt-2">12</p>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-gray-500 text-sm">Pending Reviews</h2>
            <p class="text-3xl font-bold mt-2">4</p>
        </div>

    </div>

    {{-- Main Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Recent Activities</h3>
            <ul class="space-y-3 text-gray-600">
                <li>✔ Employee John submitted report</li>
                <li>✔ Task assigned to Sarah</li>
                <li>⚠ Pending review from Michael</li>
            </ul>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>

            <div class="space-y-3">
                <button class="w-full bg-teal-600 text-white py-2 rounded-lg">
                    Assign Task
                </button>

                <a href="{{ route('supervisor.attendance.index') }}" class="block text-center w-full border border-gray-300 py-2 rounded-lg">
                    Review Attendance
                </a>

                <button class="w-full border border-gray-300 py-2 rounded-lg">
                    Generate Report
                </button>
            </div>
        </div>

    </div>

@endsection

@extends('layouts.supervisor')

@section('content')
<div class="min-h-screen bg-gray-100 p-6">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                Welcome, {{ auth()->user()->name }}
            </h1>
            <p class="text-gray-500">Supervisor Dashboard</p>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg"
            >
                Logout
            </button>
        </form>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-gray-500 text-sm">Employees</h2>
            <p class="text-3xl font-bold mt-2">25</p>
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

                <button class="w-full border border-gray-300 py-2 rounded-lg">
                    View Team
                </button>

                <button class="w-full border border-gray-300 py-2 rounded-lg">
                    Generate Report
                </button>
            </div>
        </div>

    </div>

</div>
@endsection
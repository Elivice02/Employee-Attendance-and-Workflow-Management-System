@extends('layouts.employee')

@section('title', 'Payroll Information')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <h2 class="text-lg font-semibold text-blue-900">Payroll Module - Coming Soon</h2>
                <p class="text-blue-700 mt-1">The payroll module is currently under development. This feature will be available in the next release.</p>
            </div>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">📊 Salary Information</h3>
            <p class="text-gray-600 text-sm">View your base salary, allowances, deductions, and net salary information.</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">📄 Payslips</h3>
            <p class="text-gray-600 text-sm">Download and view monthly payslips for all your salary transactions.</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">💰 Salary History</h3>
            <p class="text-gray-600 text-sm">Review your salary history and year-to-date earnings summary.</p>
        </div>
    </div>

    <a href="{{ route('employee.dashboard') }}" class="mt-6 bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition inline-block">
        Back to Dashboard
    </a>
</div>
@endsection

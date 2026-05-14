@extends('layouts.employee')

@section('title', 'Payroll Information')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Salary Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-blue-500 text-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">Base Salary</h3>
            <p class="text-2xl font-bold">₹{{ number_format($salary_info['base_salary'], 2) }}</p>
        </div>

        <div class="bg-green-500 text-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">Allowances</h3>
            <p class="text-2xl font-bold">₹{{ number_format($salary_info['allowances'], 2) }}</p>
        </div>

        <div class="bg-red-500 text-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">Deductions</h3>
            <p class="text-2xl font-bold">₹{{ number_format($salary_info['deductions'], 2) }}</p>
        </div>

        <div class="bg-purple-500 text-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">Net Salary</h3>
            <p class="text-2xl font-bold">₹{{ number_format($salary_info['net_salary'], 2) }}</p>
        </div>
    </div>

    <!-- Payslips -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 bg-gray-100 border-b">
            <h2 class="text-2xl font-bold">Recent Payslips</h2>
        </div>
        <table class="w-full">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold">Month</th>
                    <th class="px-6 py-3 text-left font-semibold">Amount</th>
                    <th class="px-6 py-3 text-left font-semibold">Status</th>
                    <th class="px-6 py-3 text-left font-semibold">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payslips as $payslip)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-6 py-3">{{ $payslip['month'] }}</td>
                        <td class="px-6 py-3">₹{{ number_format($payslip['amount'], 2) }}</td>
                        <td class="px-6 py-3">
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">{{ $payslip['status'] }}</span>
                        </td>
                        <td class="px-6 py-3">
                            <a href="#" class="text-blue-500 hover:underline">Download</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-3 text-center text-gray-500">No payslips found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <a href="{{ route('employee.dashboard') }}" class="mt-4 bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition inline-block">
        Back
    </a>
</div>
@endsection

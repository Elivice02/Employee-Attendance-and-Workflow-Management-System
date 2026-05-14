@extends('layouts.employee')

@section('title', 'Leave Requests')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-4">
        <a href="{{ route('employee.leave.create') }}" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition">
            + Request New Leave
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold">Leave Type</th>
                    <th class="px-6 py-3 text-left font-semibold">From Date</th>
                    <th class="px-6 py-3 text-left font-semibold">To Date</th>
                    <th class="px-6 py-3 text-left font-semibold">Days</th>
                    <th class="px-6 py-3 text-left font-semibold">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($leaves as $leave)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-6 py-3">{{ $leave['type'] }}</td>
                        <td class="px-6 py-3">{{ $leave['from']->format('M d, Y') }}</td>
                        <td class="px-6 py-3">{{ $leave['to']->format('M d, Y') }}</td>
                        <td class="px-6 py-3">{{ $leave['days'] }}</td>
                        <td class="px-6 py-3">
                            @if ($leave['status'] === 'Pending')
                                <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">{{ $leave['status'] }}</span>
                            @elseif ($leave['status'] === 'Approved')
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">{{ $leave['status'] }}</span>
                            @else
                                <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm">{{ $leave['status'] }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-3 text-center text-gray-500">No leave requests found</td>
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

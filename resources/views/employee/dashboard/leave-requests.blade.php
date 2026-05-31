@extends('layouts.employee')

@section('title', 'Leave Requests')

@section('content')
<div class="container mx-auto px-4 py-8">
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="mb-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Leave Requests</h1>
        <a href="{{ route('employee.leave.create') }}" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700">Request New Leave</a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left">Reference</th>
                    <th class="px-4 py-3 text-left">Type</th>
                    <th class="px-4 py-3 text-left">Period</th>
                    <th class="px-4 py-3 text-left">Days</th>
                    <th class="px-4 py-3 text-left">Stage</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($leaves as $leave)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3 font-semibold">{{ $leave->ref_no ?? 'Pending' }}</td>
                        <td class="px-4 py-3">{{ $leave->leave_type_number ? $leave->leave_type_number.'. ' : '' }}{{ $leave->leave_type_name ?? ucfirst(str_replace('_', ' ', $leave->leave_type)) }}</td>
                        <td class="px-4 py-3">{{ $leave->start_date?->format('M d, Y') }} - {{ $leave->end_date?->format('M d, Y') }}</td>
                        <td class="px-4 py-3">{{ $leave->total_days }}</td>
                        <td class="px-4 py-3">{{ ucfirst(str_replace('_', ' ', $leave->current_stage ?? 'submitted')) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-800">{{ ucfirst(str_replace('_', ' ', $leave->status)) }}</span>
                        </td>
                        <td class="px-4 py-3 flex flex-wrap gap-2">
                            <a href="{{ route('employee.leave.show', $leave) }}" class="bg-slate-700 text-white px-3 py-1 rounded hover:bg-slate-800">View</a>
                            @if ($leave->pdf_path)
                                <a href="{{ route('leave.pdf', $leave) }}" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">PDF</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">No leave requests found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

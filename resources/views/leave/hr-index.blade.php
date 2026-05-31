@extends('layouts.hr')

@section('title', 'Leave Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <h1 class="text-2xl font-bold mb-4">Leave Management</h1>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left">Reference</th>
                    <th class="px-4 py-3 text-left">Employee</th>
                    <th class="px-4 py-3 text-left">Department</th>
                    <th class="px-4 py-3 text-left">Leave Type</th>
                    <th class="px-4 py-3 text-left">Stage</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($leaves as $leave)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3 font-semibold">{{ $leave->ref_no }}</td>
                        <td class="px-4 py-3">{{ $leave->employee?->name }}</td>
                        <td class="px-4 py-3">{{ $leave->employee?->department?->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $leave->leave_type_number }}. {{ $leave->leave_type_name }}</td>
                        <td class="px-4 py-3">{{ ucfirst(str_replace('_', ' ', $leave->current_stage)) }}</td>
                        <td class="px-4 py-3">{{ ucfirst(str_replace('_', ' ', $leave->status)) }}</td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('hr.leaves.show', $leave) }}" class="bg-teal-600 text-white px-3 py-1 rounded hover:bg-teal-700">Review</a>
                            @if ($leave->pdf_path)
                                <a href="{{ route('leave.pdf', $leave) }}" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">PDF</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">No leave applications found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

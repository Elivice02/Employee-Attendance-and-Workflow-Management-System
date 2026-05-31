@extends('layouts.supervisor')

@section('title', 'Leave Recommendations')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <h1 class="text-2xl font-bold mb-4">Leave Recommendations</h1>

    <div class="bg-white rounded-lg shadow overflow-x-auto text-gray-900">
        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left">Reference</th>
                    <th class="px-4 py-3 text-left">Employee</th>
                    <th class="px-4 py-3 text-left">Leave Type</th>
                    <th class="px-4 py-3 text-left">Period</th>
                    <th class="px-4 py-3 text-left">Stage</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($leaves as $leave)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3 font-semibold">{{ $leave->ref_no }}</td>
                        <td class="px-4 py-3">{{ $leave->employee?->name }}</td>
                        <td class="px-4 py-3">{{ $leave->leave_type_number }}. {{ $leave->leave_type_name }}</td>
                        <td class="px-4 py-3">{{ $leave->start_date?->format('M d, Y') }} - {{ $leave->end_date?->format('M d, Y') }}</td>
                        <td class="px-4 py-3">{{ ucfirst(str_replace('_', ' ', $leave->current_stage)) }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('supervisor.leaves.show', $leave) }}" class="bg-slate-700 text-white px-3 py-1 rounded hover:bg-slate-800">Review</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">No leave applications assigned to you.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

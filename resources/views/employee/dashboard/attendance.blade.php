@extends('layouts.employee')

@section('title', 'Attendance Records')

@section('content')
<div class="container mx-auto px-4 py-8">
    @include('components.alert')

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold">Date</th>
                    <th class="px-6 py-3 text-left font-semibold">Reference</th>
                    <th class="px-6 py-3 text-left font-semibold">Status</th>
                    <th class="px-6 py-3 text-left font-semibold">Check In</th>
                    <th class="px-6 py-3 text-left font-semibold">Check Out</th>
                    <th class="px-6 py-3 text-left font-semibold">Letter</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $record)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-6 py-3">{{ $record->attendance_date->format('M d, Y') }}</td>
                        <td class="px-6 py-3">{{ $record->late_letter_reference ?? '-' }}</td>
                        <td class="px-6 py-3">
                            @if (str_contains($record->status, 'approved') || $record->status === 'present')
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm capitalize">{{ str_replace('_', ' ', $record->status) }}</span>
                            @elseif (str_contains($record->status, 'rejected'))
                                <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm capitalize">{{ str_replace('_', ' ', $record->status) }}</span>
                            @else
                                <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm capitalize">{{ str_replace('_', ' ', $record->status) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">{{ $record->check_in_at?->format('H:i') ?? '-' }}</td>
                        <td class="px-6 py-3">{{ $record->check_out_at?->format('H:i') ?? '-' }}</td>
                        <td class="px-6 py-3">
                            @if ($record->late_letter_final_path)
                                <a href="{{ route('attendance.letter', [$record, 'final']) }}" target="_blank"
                                   class="inline-flex items-center justify-center rounded border border-green-600 px-3 py-1.5 text-sm font-semibold text-green-700 hover:bg-green-50">
                                    View Final
                                </a>
                            @elseif ($record->late_letter_draft_path)
                                <a href="{{ route('attendance.letter', [$record, 'draft']) }}" target="_blank"
                                   class="inline-flex items-center justify-center rounded border border-teal-600 px-3 py-1.5 text-sm font-semibold text-teal-700 hover:bg-teal-50">
                                    View Draft
                                </a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-3 text-center text-gray-500">No records found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $records->links() }}
    </div>

    <a href="{{ route('employee.dashboard') }}" class="mt-4 bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition inline-block">
        Back
    </a>
</div>
@endsection

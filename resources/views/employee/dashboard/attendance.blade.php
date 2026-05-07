@extends('layouts.employee')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Attendance Records</h1>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold">Date</th>
                    <th class="px-6 py-3 text-left font-semibold">Status</th>
                    <th class="px-6 py-3 text-left font-semibold">Check In</th>
                    <th class="px-6 py-3 text-left font-semibold">Check Out</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $record)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-6 py-3">{{ $record['date']->format('M d, Y') }}</td>
                        <td class="px-6 py-3">
                            @if ($record['status'] === 'Present')
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">{{ $record['status'] }}</span>
                            @elseif ($record['status'] === 'Absent')
                                <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm">{{ $record['status'] }}</span>
                            @else
                                <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">{{ $record['status'] }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">{{ $record['check_in'] }}</td>
                        <td class="px-6 py-3">{{ $record['check_out'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-3 text-center text-gray-500">No records found</td>
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

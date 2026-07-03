@extends(auth()->user()->role === 'hr' ? 'layouts.hr' : 'layouts.supervisor')

@section('title', $title)

@section('content')
<div class="space-y-6">
    @include('components.alert')

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $title }}</h1>
            <p class="text-sm text-gray-500">Review attendance records and late check-in explanations.</p>
        </div>

        <a href="{{ route($backRoute) }}" class="bg-white border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50">
            Back
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left">Employee</th>
                    <th class="px-4 py-3 text-left">Date</th>
                    <th class="px-4 py-3 text-left">Reference</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Check In</th>
                    <th class="px-4 py-3 text-left">Check Out</th>
                    <th class="px-4 py-3 text-left">Late Explanation</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                    <th class="px-4 py-3 text-left">Review</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $record)
                    <tr class="border-t align-top">
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800">{{ $record->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $record->user->department?->name ?? 'No department' }}</p>
                        </td>
                        <td class="px-4 py-3">{{ $record->attendance_date->format('M d, Y') }}</td>
                        <td class="px-4 py-3">{{ $record->late_letter_reference ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if (str_contains($record->status, 'approved') || in_array($record->status, ['present', 'on_leave'], true))
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs capitalize">{{ str_replace('_', ' ', $record->status) }}</span>
                            @elseif (str_contains($record->status, 'rejected') || $record->status === 'absent')
                                <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs capitalize">{{ str_replace('_', ' ', $record->status) }}</span>
                            @else
                                <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs capitalize">{{ str_replace('_', ' ', $record->status) }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $record->check_in_at?->format('H:i') ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $record->check_out_at?->format('H:i') ?? '-' }}</td>
                        <td class="px-4 py-3 max-w-xs">
                            @if ($record->late_explanation)
                                <p class="text-gray-700">{{ $record->late_explanation }}</p>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 min-w-40">
                            <div class="flex flex-col gap-2">
                                @if ($record->late_letter_draft_path)
                                    <a href="{{ route('attendance.letter', [$record, 'draft']) }}" target="_blank"
                                       class="inline-flex items-center justify-center rounded border border-teal-600 px-3 py-1.5 text-xs font-semibold text-teal-700 hover:bg-teal-50">
                                        View Draft Letter
                                    </a>
                                @endif
                                @if ($record->late_letter_final_path)
                                    <a href="{{ route('attendance.letter', [$record, 'final']) }}" target="_blank"
                                       class="inline-flex items-center justify-center rounded border border-green-600 px-3 py-1.5 text-xs font-semibold text-green-700 hover:bg-green-50">
                                        View Final Letter
                                    </a>
                                @endif
                                @if ($record->late_evidence_path)
                                    <a href="{{ asset('storage/' . $record->late_evidence_path) }}" target="_blank"
                                       class="inline-flex items-center justify-center rounded border border-gray-400 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                                        View Evidence
                                    </a>
                                @endif
                                @if (! $record->late_letter_draft_path && ! $record->late_letter_final_path && ! $record->late_evidence_path)
                                    <span class="text-gray-400">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 min-w-64">
                            @if (($canReviewLate ?? false) && $record->late_review_status === 'pending')
                                <form method="POST" action="{{ route($reviewRoute, $record) }}" class="space-y-2">
                                    @csrf
                                    <select name="late_review_status" class="w-full border rounded px-2 py-1">
                                        <option value="approved">Approve</option>
                                        <option value="rejected">Reject</option>
                                        <option value="needs_more_evidence">Request more evidence</option>
                                    </select>
                                    <textarea name="late_review_note" rows="2" class="w-full border rounded px-2 py-1" placeholder="Review note"></textarea>
                                    <button class="bg-teal-600 hover:bg-teal-700 text-white px-3 py-1 rounded">
                                        Submit
                                    </button>
                                </form>
                            @elseif ($record->late_review_status)
                                <p class="capitalize text-gray-700">{{ str_replace('_', ' ', $record->late_review_status) }}</p>
                                <p class="text-xs text-gray-500">{{ $record->late_review_note }}</p>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-6 text-center text-gray-500">No attendance records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $records->links() }}
</div>
@endsection

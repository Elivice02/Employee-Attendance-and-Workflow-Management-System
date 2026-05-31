@if (($lateNotifications ?? collect())->isNotEmpty())
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-800">Late Check-In Notifications</h2>
            <a href="{{ $attendanceReviewUrl }}" class="text-sm text-teal-700 hover:underline">Review all</a>
        </div>

        <div class="space-y-3">
            @foreach ($lateNotifications as $notification)
                <div class="border rounded-lg p-4 {{ $notification->read_at ? 'bg-gray-50' : 'bg-yellow-50 border-yellow-200' }}">
                    @if ($notification->type === 'late_review_result')
                        <p class="font-semibold text-gray-800">
                            Your late check-in review is {{ str_replace('_', ' ', $notification->attendance->late_review_status) }}
                        </p>
                    @else
                        <p class="font-semibold text-gray-800">
                            {{ $notification->attendance->user->name }} checked in late
                        </p>
                    @endif
                    <p class="text-sm text-gray-600">
                        {{ $notification->attendance->attendance_date->format('M d, Y') }}
                        at {{ $notification->attendance->check_in_at?->format('H:i') }}
                    </p>
                    @if ($notification->attendance->late_review_note)
                        <p class="text-sm text-gray-500 mt-1">
                            {{ \Illuminate\Support\Str::limit($notification->attendance->late_review_note, 120) }}
                        </p>
                    @else
                        <p class="text-sm text-gray-500 mt-1">
                            {{ \Illuminate\Support\Str::limit($notification->attendance->late_explanation, 120) }}
                        </p>
                    @endif
                    @if ($notification->type === 'late_review_result' && $notification->attendance->late_review_status === 'needs_more_evidence')
                        <a href="{{ route('attendance.evidence.edit', $notification->attendance) }}"
                           class="inline-block mt-2 text-sm text-teal-700 hover:underline">
                            Submit more evidence
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif

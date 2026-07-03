@include('components.alert')

@if (session('warning'))
    <div class="bg-yellow-100 text-yellow-800 p-3 mb-4 rounded">
        {{ session('warning') }}
    </div>
@endif

@if ($errors->any())
    <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-lg shadow p-6 mb-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Today Attendance</h2>
            <p class="text-sm text-gray-500 mt-1">
                Check-in must be completed before {{ \Illuminate\Support\Str::of($attendanceSetting->work_start_time)->substr(0, 5) }}.
                Check-in and check-out are limited to the organization network.
            </p>
        </div>

        <div class="text-sm">
            @if ($todayAttendance?->check_in_at)
                <span class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded-full">
                    Checked in {{ $todayAttendance->check_in_at->format('H:i') }}
                </span>
            @else
                <span class="inline-block bg-gray-100 text-gray-700 px-3 py-1 rounded-full">
                    Not checked in
                </span>
            @endif
        </div>
    </div>

    @if ($todayAttendance?->check_in_at)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
            <div class="border rounded-lg p-4">
                <p class="text-xs uppercase text-gray-500">Status</p>
                <p class="font-semibold text-gray-800 capitalize">{{ str_replace('_', ' ', $todayAttendance->status) }}</p>
            </div>
            <div class="border rounded-lg p-4">
                <p class="text-xs uppercase text-gray-500">Reference</p>
                <p class="font-semibold text-gray-800">{{ $todayAttendance->late_letter_reference ?? '-' }}</p>
            </div>
            <div class="border rounded-lg p-4">
                <p class="text-xs uppercase text-gray-500">Check In</p>
                <p class="font-semibold text-gray-800">{{ $todayAttendance->check_in_at?->format('H:i') ?? '-' }}</p>
            </div>
            <div class="border rounded-lg p-4">
                <p class="text-xs uppercase text-gray-500">Check Out</p>
                <p class="font-semibold text-gray-800">{{ $todayAttendance->check_out_at?->format('H:i') ?? '-' }}</p>
            </div>
        </div>

        @if (! $todayAttendance->check_out_at)
            <form method="POST" action="{{ route('attendance.check-out') }}" class="mt-6">
                @csrf
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2 rounded-lg shadow">
                    Check Out
                </button>
            </form>
        @endif

        @if ($todayAttendance->late_letter_draft_path || $todayAttendance->late_letter_final_path)
            <div class="mt-4 flex flex-wrap gap-3">
                @if ($todayAttendance->late_letter_draft_path)
                    <a href="{{ route('attendance.letter', [$todayAttendance, 'draft']) }}" target="_blank"
                       class="inline-flex items-center justify-center rounded border border-teal-600 px-3 py-1.5 text-sm font-semibold text-teal-700 hover:bg-teal-50">
                        View Draft Letter
                    </a>
                @endif
                @if ($todayAttendance->late_letter_final_path)
                    <a href="{{ route('attendance.letter', [$todayAttendance, 'final']) }}" target="_blank"
                       class="inline-flex items-center justify-center rounded border border-green-600 px-3 py-1.5 text-sm font-semibold text-green-700 hover:bg-green-50">
                        View Final Letter
                    </a>
                @endif
            </div>
        @endif
    @else
        <form method="POST" action="{{ route('attendance.check-in') }}" class="mt-6">
            @csrf
            <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2 rounded-lg shadow">
                Check In
            </button>
        </form>
    @endif
</div>

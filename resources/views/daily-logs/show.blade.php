@extends($roleView === 'hr' ? 'layouts.hr' : ($roleView === 'supervisor' ? 'layouts.supervisor' : 'layouts.employee'))

@section('title', 'Daily Work Log')

@section('content')
@php
    $statusColors = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'reviewed' => 'bg-green-100 text-green-800',
        'rejected' => 'bg-red-100 text-red-800',
    ];
@endphp

<div class="max-w-5xl mx-auto px-4 py-8">
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-start gap-4 border-b pb-4 mb-5">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $dailyLog->title }}</h1>
                <p class="text-sm text-gray-600">
                    {{ $dailyLog->user?->name }} - {{ $dailyLog->log_date->format('M d, Y') }}
                </p>
            </div>
            <span class="px-3 py-1 rounded-full text-sm {{ $statusColors[$dailyLog->status] ?? 'bg-gray-100 text-gray-800' }}">
                {{ ucfirst($dailyLog->status) }}
            </span>
        </div>

        <div class="grid md:grid-cols-2 gap-4 text-sm mb-5">
            <p><strong>Submitted By:</strong> {{ $dailyLog->user?->name }}</p>
            <p><strong>Department:</strong> {{ $dailyLog->user?->department?->name ?? '-' }}</p>
            <p><strong>Linked Task:</strong> {{ $dailyLog->task?->title ?? '-' }}</p>
            <p><strong>Submitted At:</strong> {{ $dailyLog->submitted_at?->format('M d, Y H:i') }}</p>
        </div>

        <section class="mb-5">
            <h2 class="font-bold mb-2">Activities Completed</h2>
            <p class="text-sm text-gray-800 whitespace-pre-line">{{ $dailyLog->activities }}</p>
        </section>

        <section class="mb-5">
            <h2 class="font-bold mb-2">Task Progress</h2>
            <p class="text-sm text-gray-800 whitespace-pre-line">{{ $dailyLog->task_progress ?: 'No task progress recorded.' }}</p>
        </section>

        <section class="mb-5">
            <h2 class="font-bold mb-2">Challenges / Blockers</h2>
            <p class="text-sm text-gray-800 whitespace-pre-line">{{ $dailyLog->challenges ?: 'No challenges recorded.' }}</p>
        </section>

        @if ($canReview)
            <section class="border-t pt-5 mb-5">
                <h2 class="font-bold mb-3">Review Daily Log</h2>
                <form action="{{ route($roleView.'.daily-log-reviews.store', $dailyLog) }}" method="POST" class="space-y-4">
                    @csrf
                    <label class="block">
                        <span class="text-sm font-semibold">Review Decision</span>
                        <select name="status" class="mt-1 w-full rounded border-gray-300" required>
                            <option value="">Select decision</option>
                            <option value="approved">Approve</option>
                            <option value="rejected">Reject</option>
                        </select>
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold">Reviewer Comment</span>
                        <textarea name="comment" rows="4" class="mt-1 w-full rounded border-gray-300"></textarea>
                    </label>
                    <button class="bg-teal-700 text-white px-5 py-2 rounded hover:bg-teal-800">Save Review</button>
                </form>
            </section>
        @endif

        <section class="border-t pt-5">
            <h2 class="font-bold mb-3">Review History</h2>
            <div class="space-y-3">
                @forelse ($dailyLog->reviews as $review)
                    <div class="border rounded p-3 bg-gray-50">
                        <div class="flex justify-between gap-3 text-xs text-gray-500 mb-2">
                            <span>{{ $review->reviewer?->name }} - {{ ucfirst($review->reviewer_role) }}</span>
                            <span>{{ ucfirst($review->status) }} on {{ $review->reviewed_at->format('M d, Y H:i') }}</span>
                        </div>
                        <p class="text-sm text-gray-800 whitespace-pre-line">{{ $review->comment ?: 'No comment provided.' }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No review has been recorded yet.</p>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection

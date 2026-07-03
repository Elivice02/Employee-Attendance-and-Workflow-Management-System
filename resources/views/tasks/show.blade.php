@extends($roleView === 'hr' ? 'layouts.hr' : ($roleView === 'supervisor' ? 'layouts.supervisor' : 'layouts.employee'))

@section('title', 'Task Details')

@section('content')
@php
    $statusColors = [
        'assigned' => 'bg-yellow-100 text-yellow-800',
        'pending_review' => 'bg-amber-100 text-amber-800',
        'in_revision' => 'bg-red-100 text-red-800',
        'in_progress' => 'bg-blue-100 text-blue-800',
        'completed' => 'bg-green-100 text-green-800',
        'archived' => 'bg-gray-100 text-gray-800',
    ];
    $canUpdate = $task->assigned_to === auth()->id() && $task->status !== 'completed';
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
        <div class="flex justify-between gap-4 items-start border-b pb-4 mb-5">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $task->title }}</h1>
                <p class="text-sm text-gray-600 mt-1">{{ ucfirst(str_replace('_', ' ', $task->scope)) }} task</p>
            </div>
            <span class="px-3 py-1 rounded-full text-sm {{ $statusColors[$task->status] ?? 'bg-gray-100 text-gray-800' }}">
                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
            </span>
        </div>

        <div class="grid md:grid-cols-2 gap-4 text-sm mb-5">
            <p><strong>Assigned To:</strong> {{ $task->assignee?->name }}</p>
            <p><strong>Assigned By:</strong> {{ $task->assigner?->name }}</p>
            <p><strong>Department:</strong> {{ $task->assignee?->department?->name ?? '-' }}</p>
            <p><strong>Due Date:</strong> {{ $task->due_date?->format('M d, Y') ?? '-' }}</p>
            <p><strong>Priority:</strong> {{ ucfirst($task->priority) }}</p>
            <p><strong>Completed At:</strong> {{ $task->completed_at?->format('M d, Y H:i') ?? '-' }}</p>
        </div>

        <div class="mb-5">
            <h2 class="font-bold mb-2">Description</h2>
            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $task->description ?: 'No description provided.' }}</p>
        </div>

        @if ($roleView === 'supervisor' && $task->assigned_by === auth()->id() && $task->status !== 'completed')
            <a href="{{ route('supervisor.tasks.edit', $task) }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mb-5">Edit Task</a>
        @endif

        @if ($canUpdate)
            <div class="border-t pt-5 mb-5">
                <h2 class="font-bold mb-3">Add Progress Update</h2>
                <form action="{{ $roleView === 'employee' ? route('employee.tasks.updates.store', $task) : route('supervisor.tasks.updates.store', $task) }}" method="POST" class="space-y-4">
                    @csrf
                    <label class="block">
                        <span class="text-sm font-semibold">Progress Notes</span>
                        <textarea name="progress_notes" rows="4" class="mt-1 w-full rounded border-gray-300" required></textarea>
                    </label>

                    <label class="block">
                        <span class="text-sm font-semibold">Update Action</span>
                        <select name="action" class="mt-1 w-full rounded border-gray-300" required>
                            <option value="note">Save progress note only</option>
                            @if ($task->status === 'assigned')
                                <option value="start">Start task</option>
                            @endif
                            @if ($task->status === 'in_progress')
                                <option value="complete">Mark completed</option>
                            @endif
                        </select>
                    </label>

                    <button class="bg-teal-700 text-white px-5 py-2 rounded hover:bg-teal-800">Save Update</button>
                </form>
            </div>
        @endif

        <div class="border-t pt-5">
            <h2 class="font-bold mb-3">Progress History</h2>
            <div class="space-y-3">
                @forelse ($task->updates as $update)
                    <div class="border rounded p-3 bg-gray-50">
                        <div class="flex justify-between gap-3 text-xs text-gray-500 mb-2">
                            <span>{{ $update->user?->name }} - {{ $update->created_at->format('M d, Y H:i') }}</span>
                            <span>{{ ucfirst(str_replace('_', ' ', $update->status_after_update)) }}</span>
                        </div>
                        <p class="text-sm text-gray-800 whitespace-pre-line">{{ $update->progress_notes }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No progress updates yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

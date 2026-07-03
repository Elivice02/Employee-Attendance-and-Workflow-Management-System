@extends($roleView === 'hr' ? 'layouts.hr' : ($roleView === 'supervisor' ? 'layouts.supervisor' : 'layouts.employee'))

@section('title', $title)

@section('content')
@php
    $statusColors = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'in_progress' => 'bg-blue-100 text-blue-800',
        'completed' => 'bg-green-100 text-green-800',
        'overdue' => 'bg-red-100 text-red-800',
    ];
    $priorityColors = [
        'low' => 'bg-gray-100 text-gray-800',
        'medium' => 'bg-sky-100 text-sky-800',
        'high' => 'bg-orange-100 text-orange-800',
        'urgent' => 'bg-red-100 text-red-800',
    ];
@endphp

<div class="max-w-7xl mx-auto px-4 py-8">
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="mb-5 flex items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $title }}</h1>
            <p class="text-sm text-gray-600">Track assigned work, due dates, priority, and progress updates.</p>
        </div>

        @if ($roleView === 'supervisor')
            <a href="{{ route('supervisor.tasks.create') }}" class="bg-teal-700 text-white px-4 py-2 rounded hover:bg-teal-800">Assign Task</a>
        @elseif ($roleView === 'hr')
            <a href="{{ route('hr.tasks.create') }}" class="bg-teal-700 text-white px-4 py-2 rounded hover:bg-teal-800">Assign Compliance Task</a>
        @endif
    </div>

    <div class="bg-white border rounded-lg shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left">Task</th>
                    <th class="px-4 py-3 text-left">Assigned To</th>
                    <th class="px-4 py-3 text-left">Assigned By</th>
                    <th class="px-4 py-3 text-left">Due Date</th>
                    <th class="px-4 py-3 text-left">Priority</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tasks as $task)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="font-semibold text-gray-900">{{ $task->title }}</div>
                            <div class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $task->scope)) }}</div>
                        </td>
                        <td class="px-4 py-3">{{ $task->assignee?->name }}</td>
                        <td class="px-4 py-3">{{ $task->assigner?->name }}</td>
                        <td class="px-4 py-3">{{ $task->due_date?->format('M d, Y') ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-3 py-1 rounded-full {{ $priorityColors[$task->priority] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-3 py-1 rounded-full {{ $statusColors[$task->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 flex flex-wrap gap-2">
                            @if ($roleView === 'hr')
                                <a href="{{ route('hr.tasks.show', $task) }}" class="bg-slate-700 text-white px-3 py-1 rounded hover:bg-slate-800">View</a>
                            @elseif ($roleView === 'supervisor')
                                <a href="{{ route('supervisor.tasks.show', $task) }}" class="bg-slate-700 text-white px-3 py-1 rounded hover:bg-slate-800">View</a>
                                @if ($task->assigned_by === auth()->id() && $task->status !== 'completed')
                                    <a href="{{ route('supervisor.tasks.edit', $task) }}" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Edit</a>
                                @endif
                            @else
                                <a href="{{ route('employee.tasks.show', $task) }}" class="bg-slate-700 text-white px-3 py-1 rounded hover:bg-slate-800">View</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">No tasks found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

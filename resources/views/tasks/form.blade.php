@extends($roleView === 'hr' ? 'layouts.hr' : 'layouts.supervisor')

@section('title', $title)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
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
        <h1 class="text-2xl font-bold text-gray-900 mb-5">{{ $title }}</h1>

        <form action="{{ $action }}" method="POST" class="space-y-5">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif

            <label class="block">
                <span class="text-sm font-semibold">{{ $roleView === 'hr' ? 'Assign To Supervisor' : 'Assign To Employee' }}</span>
                <select name="assigned_to" class="mt-1 w-full rounded border-gray-300" required>
                    <option value="">Select assignee</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}" @selected((string) old('assigned_to', $task?->assigned_to) === (string) $employee->id)>
                            {{ $employee->name }}{{ $employee->department ? ' - '.$employee->department->name : '' }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Task Title</span>
                <input name="title" value="{{ old('title', $task?->title) }}" class="mt-1 w-full rounded border-gray-300" required>
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Task Description</span>
                <textarea name="description" rows="5" class="mt-1 w-full rounded border-gray-300">{{ old('description', $task?->description) }}</textarea>
            </label>

            <div class="grid md:grid-cols-2 gap-4">
                <label class="block">
                    <span class="text-sm font-semibold">Due Date</span>
                    <input type="date" name="due_date" value="{{ old('due_date', $task?->due_date?->format('Y-m-d')) }}" class="mt-1 w-full rounded border-gray-300">
                </label>

                <label class="block">
                    <span class="text-sm font-semibold">Priority</span>
                    <select name="priority" class="mt-1 w-full rounded border-gray-300" required>
                        @foreach (['low', 'medium', 'high', 'critical'] as $priority)
                            <option value="{{ $priority }}" @selected(old('priority', $task?->priority ?? 'medium') === $priority)>
                                {{ ucfirst($priority) }}
                            </option>
                        @endforeach
                    </select>
                </label>
            </div>

            <div class="flex gap-3">
                <button class="bg-teal-700 text-white px-5 py-2 rounded hover:bg-teal-800">Save Task</button>
                <a href="{{ $roleView === 'hr' ? route('hr.tasks.index') : route('supervisor.tasks.index') }}" class="bg-gray-600 text-white px-5 py-2 rounded hover:bg-gray-700">Back</a>
            </div>
        </form>
    </div>
</div>
@endsection

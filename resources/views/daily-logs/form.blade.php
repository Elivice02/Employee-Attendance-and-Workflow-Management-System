@extends($roleView === 'supervisor' ? 'layouts.supervisor' : 'layouts.employee')

@section('title', 'Submit Daily Work Log')

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
        <h1 class="text-2xl font-bold text-gray-900 mb-5">Submit Daily Work Log</h1>

        <form action="{{ $action }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid md:grid-cols-2 gap-4">
                <label class="block">
                    <span class="text-sm font-semibold">Log Date</span>
                    <input type="date" name="log_date" value="{{ old('log_date', now()->toDateString()) }}" class="mt-1 w-full rounded border border-gray-300 px-3 py-2" required>
                </label>

                <label class="block">
                    <span class="text-sm font-semibold">Related Assigned Task</span>
                    <select name="task_id" class="mt-1 w-full rounded border border-gray-300 px-3 py-2">
                        <option value="">No linked task</option>
                        @foreach ($tasks as $task)
                            <option value="{{ $task->id }}" @selected((string) old('task_id') === (string) $task->id)>
                                {{ $task->title }} - {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </option>
                        @endforeach
                    </select>
                </label>
            </div>

            <label class="block">
                <span class="text-sm font-semibold">Short Summary Title</span>
                <input name="title" value="{{ old('title') }}" class="mt-1 w-full rounded border border-gray-300 px-3 py-2" required>
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Activities Completed Today</span>
                <textarea name="activities" rows="5" class="mt-1 w-full rounded border border-gray-300 px-3 py-2" required>{{ old('activities') }}</textarea>
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Task Progress Made</span>
                <textarea name="task_progress" rows="4" class="mt-1 w-full rounded border border-gray-300 px-3 py-2">{{ old('task_progress') }}</textarea>
            </label>

            <label class="block">
                <span class="text-sm font-semibold">Challenges / Blockers</span>
                <textarea name="challenges" rows="4" class="mt-1 w-full rounded border border-gray-300 px-3 py-2">{{ old('challenges') }}</textarea>
            </label>

            <div class="flex gap-3">
                <button class="bg-teal-700 text-white px-5 py-2 rounded hover:bg-teal-800">Submit Log</button>
                <a href="{{ route($roleView.'.daily-logs.index') }}" class="bg-gray-600 text-white px-5 py-2 rounded hover:bg-gray-700">Back</a>
            </div>
        </form>
    </div>
</div>
@endsection

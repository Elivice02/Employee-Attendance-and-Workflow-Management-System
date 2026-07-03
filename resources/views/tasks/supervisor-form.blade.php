@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">{{ $title ?? 'Manage Task' }}</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('supervisor.tasks.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Task Information</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ $action }}">
                        @csrf
                        @if ($method === 'PUT')
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label for="title" class="form-label">Task Title *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                id="title" name="title" value="{{ $task?->title ?? old('title') }}" required>
                            @error('title')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="4" required>{{ $task?->description ?? old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="assigned_to" class="form-label">Assign To *</label>
                                <select class="form-select @error('assigned_to') is-invalid @enderror" 
                                    id="assigned_to" name="assigned_to" required>
                                    <option value="">Select an employee</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" 
                                            {{ (old('assigned_to') ?? $task?->assigned_to) == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="priority" class="form-label">Priority *</label>
                                <select class="form-select @error('priority') is-invalid @enderror" 
                                    id="priority" name="priority" required>
                                    <option value="">Select priority</option>
                                    <option value="low" {{ (old('priority') ?? $task?->priority) === 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ (old('priority') ?? $task?->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ (old('priority') ?? $task?->priority) === 'high' ? 'selected' : '' }}>High</option>
                                    <option value="critical" {{ (old('priority') ?? $task?->priority) === 'critical' ? 'selected' : '' }}>Critical</option>
                                </select>
                                @error('priority')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                    id="start_date" name="start_date" value="{{ $task?->start_date?->format('Y-m-d') ?? old('start_date') }}">
                                @error('start_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                    id="end_date" name="end_date" value="{{ $task?->end_date?->format('Y-m-d') ?? old('end_date') }}">
                                @error('end_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="due_date" class="form-label">Due Date *</label>
                                <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                    id="due_date" name="due_date" value="{{ $task?->due_date?->format('Y-m-d') ?? old('due_date') }}" required>
                                @error('due_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('supervisor.tasks.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

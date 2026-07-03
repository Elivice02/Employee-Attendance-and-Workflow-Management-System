@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">Daily Progress Update</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('employee.tasks.show', $task) }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ $task->title }}</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Status</h6>
                            <p>
                                <span class="badge" style="background-color: {{ $task->getStatusColor() }}; font-size: 1em;">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Task Progress</h6>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar" role="progressbar" 
                                    style="width: {{ $task->completion_percentage ?? 0 }}%"
                                    aria-valuenow="{{ $task->completion_percentage ?? 0 }}" 
                                    aria-valuemin="0" aria-valuemax="100">
                                    {{ $task->completion_percentage ?? 0 }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Submit Today's Progress</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('employee.tasks.progress.store', $task) }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="work_done" class="form-label">Work Completed Today *</label>
                            <textarea class="form-control @error('work_done') is-invalid @enderror" 
                                id="work_done" name="work_done" rows="4" required 
                                placeholder="Describe what was accomplished today...">{{ old('work_done') }}</textarea>
                            @error('work_done')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="completion_percentage" class="form-label">Completion Percentage (0-100) *</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('completion_percentage') is-invalid @enderror" 
                                        id="completion_percentage" name="completion_percentage" min="0" max="100" 
                                        value="{{ old('completion_percentage') }}" required>
                                    <span class="input-group-text">%</span>
                                </div>
                                @error('completion_percentage')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                                <small class="text-muted">Update this as you progress (0% = not started, 100% = complete)</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="challenges" class="form-label">Challenges or Blockers</label>
                                <textarea class="form-control @error('challenges') is-invalid @enderror" 
                                    id="challenges" name="challenges" rows="3" 
                                    placeholder="Any issues encountered (optional)...">{{ old('challenges') }}</textarea>
                                @error('challenges')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="attachment" class="form-label">Attachment / Evidence</label>
                            <input type="file" class="form-control @error('attachment') is-invalid @enderror" 
                                id="attachment" name="attachment">
                            <small class="text-muted">Upload evidence of work (documents, screenshots, etc.) - Optional</small>
                            @error('attachment')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('employee.tasks.show', $task) }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Submit Progress
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Task Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted text-uppercase mb-1">Assigned By</h6>
                        <p>{{ $task->assigner->name ?? 'Unknown' }}</p>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-muted text-uppercase mb-1">Due Date</h6>
                        <p>{{ $task->due_date ? $task->due_date->format('M d, Y') : 'N/A' }}</p>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-muted text-uppercase mb-1">Days Until Deadline</h6>
                        <p>
                            @if ($task->due_date)
                                @if ($task->due_date->isPast())
                                    <span class="badge bg-danger">{{ abs($task->daysUntilDeadline()) }} days overdue</span>
                                @else
                                    <span class="badge bg-success">{{ $task->daysUntilDeadline() }} days remaining</span>
                                @endif
                            @else
                                N/A
                            @endif
                        </p>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-muted text-uppercase mb-1">Priority</h6>
                        <p>
                            @switch($task->priority)
                                @case('critical')
                                    <span class="badge bg-danger">Critical</span>
                                @break
                                @case('high')
                                    <span class="badge bg-warning">High</span>
                                @break
                                @case('medium')
                                    <span class="badge bg-info">Medium</span>
                                @break
                                @default
                                    <span class="badge bg-success">Low</span>
                            @endswitch
                        </p>
                    </div>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Guidelines</h6>
                </div>
                <div class="card-body">
                    <ul class="small">
                        <li>Submit progress <strong>every working day</strong></li>
                        <li>Be accurate with completion percentage</li>
                        <li>Document any challenges or blockers</li>
                        <li>Attach supporting evidence when possible</li>
                        <li>Your supervisor will review and provide feedback</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">{{ $task->title }}</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('employee.tasks.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Task Details -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Task Details</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase mb-2">Status</h6>
                            <p>
                                <span class="badge" style="background-color: {{ $task->getStatusColor() }}; font-size: 1em;">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase mb-2">Priority</h6>
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

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase mb-2">Start Date</h6>
                            <p>{{ $task->start_date ? $task->start_date->format('M d, Y') : 'Not started' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase mb-2">Due Date</h6>
                            <p>{{ $task->due_date ? $task->due_date->format('M d, Y') : 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase mb-2">End Date</h6>
                            <p>{{ $task->end_date ? $task->end_date->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase mb-2">Days Until Deadline</h6>
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
                    </div>

                    <div class="mb-3">
                        <h6 class="text-muted text-uppercase mb-2">Description</h6>
                        <p>{{ $task->description }}</p>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-muted text-uppercase mb-2">Assigned By</h6>
                        <p>{{ $task->assigner->name ?? 'Unknown' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Progress Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Progress</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted text-uppercase mb-2">Overall Progress</h6>
                        <div class="progress" style="height: 30px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                style="width: {{ $task->completion_percentage ?? 0 }}%"
                                aria-valuenow="{{ $task->completion_percentage ?? 0 }}" 
                                aria-valuemin="0" aria-valuemax="100">
                                <strong>{{ $task->completion_percentage ?? 0 }}%</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                </div>
                <div class="card-body">
                    @if ($canStart)
                        <form action="{{ route('employee.tasks.start', $task) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-play"></i> Start Task
                            </button>
                        </form>
                    @endif

                    @if ($canSubmitProgress)
                        <a href="{{ route('employee.tasks.progress.create', $task) }}" 
                            class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-plus"></i> Update Progress
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

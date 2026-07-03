@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">{{ $task->title }}</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('supervisor.tasks.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-8">
            <!-- Task Details -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Task Details</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase mb-2">Assigned To</h6>
                            <p>{{ $task->assignee->name ?? 'Unknown' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase mb-2">Status</h6>
                            <p>
                                <span class="badge" style="background-color: {{ $task->getStatusColor() }}; font-size: 1em;">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
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
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase mb-2">Duration</h6>
                            <p>{{ $task->getDurationDays() }} days</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-muted text-uppercase mb-2">Description</h6>
                        <p>{{ $task->description }}</p>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase mb-2">Start Date</h6>
                            <p>{{ $task->start_date ? $task->start_date->format('M d, Y') : 'Not started' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase mb-2">End Date</h6>
                            <p>{{ $task->end_date ? $task->end_date->format('M d, Y') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Timeline -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Progress Timeline 
                        @if ($unreviewedProgress > 0)
                            <span class="badge bg-warning">{{ $unreviewedProgress }} Unreviewed</span>
                        @endif
                    </h6>
                </div>
                <div class="card-body">
                    @if ($progress->isEmpty())
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle"></i> No progress records yet.
                        </div>
                    @else
                        <div class="timeline">
                            @foreach ($progress as $p)
                                <div class="timeline-item mb-4">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>{{ $p->progress_date->format('M d, Y') }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $p->getDayOfWeek() }}</small>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <h6>Work Done</h6>
                                                    <p>{{ $p->work_done }}</p>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h6>Completion</h6>
                                                            <div class="progress">
                                                                <div class="progress-bar" role="progressbar" 
                                                                    style="width: {{ $p->completion_percentage }}%"
                                                                    aria-valuenow="{{ $p->completion_percentage }}" 
                                                                    aria-valuemin="0" aria-valuemax="100">
                                                                    {{ $p->completion_percentage }}%
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6>Status</h6>
                                                            @if ($p->supervisor_reviewed_at)
                                                                <span class="badge bg-success">Reviewed</span>
                                                            @else
                                                                <span class="badge bg-warning">Pending Review</span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    @if ($p->challenges)
                                                        <div class="mt-2">
                                                            <h6>Challenges</h6>
                                                            <p class="text-danger">{{ $p->challenges }}</p>
                                                        </div>
                                                    @endif

                                                    @if (!$p->supervisor_reviewed_at)
                                                        <form method="POST" 
                                                            action="{{ route('supervisor.tasks.progress.review', [$task, $p]) }}" 
                                                            class="mt-3">
                                                            @csrf
                                                            <div class="mb-2">
                                                                <textarea name="remarks" class="form-control" 
                                                                    rows="2" placeholder="Add remarks (optional)"></textarea>
                                                            </div>
                                                            <button type="submit" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-check"></i> Mark as Reviewed
                                                            </button>
                                                        </form>
                                                    @else
                                                        <div class="mt-2 alert alert-light">
                                                            <small>Reviewed by {{ $p->reviewer->name ?? 'Unknown' }}
                                                                {{ $p->supervisor_reviewed_at->format('M d, Y H:i') }}</small>
                                                            @if ($p->remarks)
                                                                <br><small>Remarks: {{ $p->remarks }}</small>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Progress Summary -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Overall Progress</h6>
                </div>
                <div class="card-body">
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

            <!-- Actions -->
            @if ($task->status === 'pending_review')
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('supervisor.tasks.approve', $task) }}" class="mb-2">
                            @csrf
                            <textarea name="remarks" class="form-control mb-2" rows="2" 
                                placeholder="Final remarks (optional)"></textarea>
                            <button type="submit" class="btn btn-success w-100 mb-2">
                                <i class="fas fa-check-circle"></i> Approve Task
                            </button>
                        </form>

                        <form method="POST" action="{{ route('supervisor.tasks.reject', $task) }}">
                            @csrf
                            <textarea name="feedback" class="form-control mb-2" rows="2" 
                                placeholder="Feedback for revision (required)" required></textarea>
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="fas fa-exclamation-triangle"></i> Send Back for Revision
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .timeline-item {
        padding-left: 20px;
        border-left: 2px solid #ddd;
        position: relative;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -8px;
        top: 0;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: #4e73df;
    }
</style>
@endsection

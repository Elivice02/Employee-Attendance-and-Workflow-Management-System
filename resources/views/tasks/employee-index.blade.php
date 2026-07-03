@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">My Tasks</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('employee.tasks.index') }}" class="btn btn-sm btn-secondary me-2">
                <i class="fas fa-sync"></i> Refresh
            </a>
        </div>
    </div>

    <!-- Task Stats -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-primary font-weight-bold text-uppercase mb-1">Active Tasks</div>
                    <div class="h3 mb-0">{{ $activeCount ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-info font-weight-bold text-uppercase mb-1">Assigned</div>
                    <div class="h3 mb-0">{{ $assignedCount ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-success font-weight-bold text-uppercase mb-1">Total Tasks</div>
                    <div class="h3 mb-0">{{ $tasks->total() ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Task List</h6>
        </div>
        <div class="card-body">
            @if ($tasks->isEmpty())
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-info-circle"></i> No tasks assigned yet.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Due Date</th>
                                <th>Progress</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tasks as $task)
                                <tr>
                                    <td>
                                        <strong>{{ $task->title }}</strong>
                                        <br>
                                        <small class="text-muted">{{ Str::limit($task->description, 50) }}</small>
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $task->getStatusColor() }}">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                    </td>
                                    <td>
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
                                    </td>
                                    <td>
                                        {{ $task->due_date ? $task->due_date->format('M d, Y') : 'N/A' }}
                                        @if ($task->due_date && $task->due_date->isPast() && $task->status !== 'completed')
                                            <br><small class="text-danger"><i class="fas fa-exclamation-triangle"></i> Overdue</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" 
                                                style="width: {{ $task->completion_percentage ?? 0 }}%"
                                                aria-valuenow="{{ $task->completion_percentage ?? 0 }}" 
                                                aria-valuemin="0" aria-valuemax="100">
                                                {{ $task->completion_percentage ?? 0 }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('employee.tasks.show', $task) }}" 
                                            class="btn btn-sm btn-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if ($task->status === 'assigned')
                                            <form action="{{ route('employee.tasks.start', $task) }}" method="POST" 
                                                style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" 
                                                    title="Start Task">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            </form>
                                        @elseif (in_array($task->status, ['in_progress', 'in_revision']))
                                            <a href="{{ route('employee.tasks.progress.create', $task) }}" 
                                                class="btn btn-sm btn-warning" title="Add Progress">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $tasks->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .border-left-primary {
        border-left: 4px solid #4e73df;
    }
    .border-left-info {
        border-left: 4px solid #17a2b8;
    }
    .border-left-success {
        border-left: 4px solid #1cc88a;
    }
</style>
@endsection

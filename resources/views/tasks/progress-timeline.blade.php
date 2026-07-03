@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">{{ $task->title }} - Progress Timeline</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('supervisor.tasks.show', $task) }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daily Progress Records</h6>
        </div>
        <div class="card-body">
            @if ($progressRecords->isEmpty())
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-info-circle"></i> No progress records found.
                </div>
            @else
                <div class="timeline">
                    @foreach ($progressRecords as $record)
                        <div class="timeline-item mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>{{ $record->progress_date->format('M d, Y') }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $record->getDayOfWeek() }}</small>
                                </div>
                                <div class="col-md-9">
                                    <div class="card border">
                                        <div class="card-body">
                                            <h6>Work Done</h6>
                                            <p>{{ $record->work_done }}</p>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <h6>Completion</h6>
                                                    <div class="progress">
                                                        <div class="progress-bar" role="progressbar" 
                                                            style="width: {{ $record->completion_percentage }}%"
                                                            aria-valuenow="{{ $record->completion_percentage }}" 
                                                            aria-valuemin="0" aria-valuemax="100">
                                                            {{ $record->completion_percentage }}%
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6>Status</h6>
                                                    @if ($record->supervisor_reviewed_at)
                                                        <span class="badge bg-success">Reviewed</span>
                                                    @else
                                                        <span class="badge bg-warning">Pending Review</span>
                                                    @endif
                                                </div>
                                            </div>

                                            @if ($record->challenges)
                                                <div class="mb-3">
                                                    <h6>Challenges</h6>
                                                    <p class="text-danger">{{ $record->challenges }}</p>
                                                </div>
                                            @endif

                                            @if ($record->attachment_path)
                                                <div class="mb-3">
                                                    <a href="{{ Storage::url($record->attachment_path) }}" 
                                                        class="btn btn-sm btn-outline-primary" target="_blank">
                                                        <i class="fas fa-download"></i> View Attachment
                                                    </a>
                                                </div>
                                            @endif

                                            @if ($record->supervisor_reviewed_at && $record->remarks)
                                                <div class="alert alert-light">
                                                    <small><strong>Supervisor Remarks:</strong> {{ $record->remarks }}</small>
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

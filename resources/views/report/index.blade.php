@extends('layouts.app')
@section('title', 'POS')
@section('page-title', 'Report')
@section('content')

{{-- Success Alert --}}
@if(session('success'))
<div class="custom-alert-success">
    <div class="alert-icon">
        <i class="fa-solid fa-circle-check"></i>
    </div>

    <div class="alert-content">
        <h6>Success</h6>
        <p>{{ session('success') }}</p>
    </div>

    <button class="alert-close" onclick="this.parentElement.remove()">
        <i class="fa-solid fa-xmark"></i>
    </button>
</div>
@endif


<div class="card mb-4" style="width: 75%;">

    <div class="section-header">

        <h4>Create Report</h4>

    </div>

    <form
        method="POST"
        action="{{ route('reports.store') }}">

        @csrf

        <div class="form-group">

            <label class="form-label">
                Title
            </label>

            <input
                type="text"
                name="title"
                class="form-input"
                placeholder="Coffee machine leaking ..."
                required>

        </div>

        <div class="form-group">

            <label class="form-label">
                Message
            </label>

            <textarea
                name="message"
                class="form-input"
                rows="4"
                placeholder="Describe the issue ..."
                required></textarea>

        </div>

        <div class="form-group">

            <label class="form-label">

                Priority

            </label>

            <select
                name="priority"
                class="form-select">

                <option value="low">
                    Low
                </option>

                <option value="medium">
                    Medium
                </option>

                <option value="high">
                    High
                </option>

                <option value="urgent">
                    Urgent
                </option>

            </select>

        </div>

        <button
            class="btn btn-primary">

            Submit Report

        </button>

    </form>

</div>

<div class="report-list mt-3">

    @foreach($reports as $report)

    <div class="report-card">

        <div class="report-header">

            <div>

                <h4>

                    {{ $report->title }}

                </h4>

                <small>

                    {{ $report->user->name }}

                    •

                    {{ ucfirst($report->user->role) }}

                </small>

            </div>

            <div>

                <span class="priority priority-{{ $report->priority }}">

                    {{ ucfirst($report->priority) }}

                </span>

            </div>

        </div>

        <p class="report-message">

            {{ $report->message }}

        </p>

        <div class="report-footer">

            <span>

                {{ $report->created_at->diffForHumans() }}

            </span>

            <div>

                <span class="status-badge status-{{ $report->status }}">

                    @if(auth()->user()->role == 'owner'
                    && $report->status !== 'open')

                    {{ ucfirst($report->status) }}

                    @endif


                </span>

                @if(auth()->user()->role == 'owner'
                && $report->status == 'open')

                <form
                    action="{{ route('reports.destroy', $report) }}"
                    method="POST">

                    @csrf
                    @method('DELETE')

                    <button
                        class="btn btn-success"
                        onclick="return confirm('Mark this report as resolved?')">

                        <i class="fa-solid fa-check me-1"></i>
                        Resolve

                    </button>

                </form>

                @endif

            </div>

        </div>

    </div>

    @endforeach

</div>

@endsection
@extends('admin.layouts.layout')

@section('title', 'Admin - Request Details')

@section('main-content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold mb-3">
            Request / {{ $requestRecord->subject }}
        </h4>

        <div class="card">
            <div class="card-body">
                <p><strong>Employee:</strong> {{ $requestRecord->employee->employee_code ?? '' }} -
                    {{ $requestRecord->employee->first_name ?? '' }} {{ $requestRecord->employee->last_name ?? '' }}</p>
                <p><strong>Type:</strong> {{ $requestRecord->requestType->name ?? '-' }}</p>
                <p><strong>Workflow:</strong> {{ $requestRecord->requestType->workflow->name ?? '-' }}</p>
                <p><strong>Status:</strong> {{ strtoupper($requestRecord->status) }}</p>
                <p><strong>Requested Date:</strong>
                    {{ $requestRecord->requested_date ? \Carbon\Carbon::parse($requestRecord->requested_date)->format('Y-m-d') : '-' }}
                </p>
                <p><strong>Details:</strong><br>{{ $requestRecord->details ?? '-' }}</p>
                <p><strong>Admin Note:</strong><br>{{ $requestRecord->admin_note ?? '-' }}</p>

                @if ($requestRecord->approvalRequest)
                    <hr>
                    <p><strong>Approval Status:</strong> {{ strtoupper($requestRecord->approvalRequest->status) }}</p>
                @endif

                <a href="{{ route('admin.requests.index') }}" class="btn btn-outline-secondary">Back</a>
            </div>
        </div>
    </div>
@endsection

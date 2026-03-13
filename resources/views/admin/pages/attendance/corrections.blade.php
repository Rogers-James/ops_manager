@extends('admin.layouts.layout')

@section('title', 'Admin - Corrections')

@section('main-content')


    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold mb-0">
                <span class="text-muted fw-light">Time Management /</span> Corrections / Regularization
            </h4>

            <form method="GET" action="{{ route('admin.attendance.corrections') }}" class="d-flex gap-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach (['pending', 'approved', 'rejected'] as $s)
                        <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>{{ strtoupper($s) }}
                        </option>
                    @endforeach
                </select>
                <button class="btn btn-primary btn-sm">Filter</button>
            </form>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Attendance Requests</h5>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Requested First In</th>
                            <th>Requested Last Out</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody class="table-border-bottom-0">
                        @forelse($requests as $i => $r)
                            <tr>
                                <td>{{ $requests->firstItem() + $i }}</td>
                                <td>
                                    <strong>{{ $r->employee?->employee_code }}</strong><br>
                                    <small class="text-muted">{{ $r->employee?->first_name }}
                                        {{ $r->employee?->last_name }}</small>
                                </td>
                                <td>{{ $r->date?->format('Y-m-d') }}</td>
                                <td>{{ $r->requested_first_in?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td>{{ $r->requested_last_out?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td><small class="text-muted">{{ $r->reason ?? '-' }}</small></td>
                                <td>
                                    @php
                                        $badge = match ($r->status) {
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            default => 'info',
                                        };
                                    @endphp
                                    <span class="badge bg-label-{{ $badge }}">{{ strtoupper($r->status) }}</span>
                                </td>
                                <td>
                                    @if ($r->status === 'pending')
                                        <form class="d-inline" method="POST"
                                            action="{{ route('admin.attendance.corrections.status', $r->id) }}"
                                            onsubmit="return confirm('Approve this correction?')">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="approved">
                                            <button class="btn btn-sm btn-success">Approve</button>
                                        </form>

                                        <form class="d-inline" method="POST"
                                            action="{{ route('admin.attendance.corrections.status', $r->id) }}"
                                            onsubmit="return confirm('Reject this correction?')">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="rejected">
                                            <button class="btn btn-sm btn-danger">Reject</button>
                                        </form>
                                    @else
                                        <small class="text-muted">Done</small>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">No correction requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $requests->links() }}
            </div>
        </div>
    </div>

@endsection

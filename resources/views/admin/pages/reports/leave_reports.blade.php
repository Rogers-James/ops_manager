@extends('admin.layouts.layout')

@section('title', 'Admin - Leave Reports')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">

        <h4 class="fw-bold mb-3">
            <span class="text-muted fw-light">Reports /</span> Leave Reports
        </h4>

        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label">Employee</label>
                        <select name="employee_id" class="form-select">
                            <option value="">All</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}"
                                    {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->employee_code }} - {{ $employee->first_name }} {{ $employee->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            @foreach (['pending', 'approved', 'rejected', 'cancelled'] as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary w-100">Filter</button>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <a href="{{ route('admin.reports.leave') }}" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Leave Type</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Days</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($requests as $i => $request)
                            <tr>
                                <td>{{ $requests->firstItem() + $i }}</td>
                                <td>{{ $request->employee->employee_code ?? '' }} -
                                    {{ $request->employee->first_name ?? '' }} {{ $request->employee->last_name ?? '' }}
                                </td>
                                <td>{{ $request->leaveType->name ?? '-' }}</td>
                                <td>{{ $request->start_date ? \Carbon\Carbon::parse($request->start_date)->format('Y-m-d') : '-' }}
                                </td>
                                <td>{{ $request->end_date ? \Carbon\Carbon::parse($request->end_date)->format('Y-m-d') : '-' }}
                                </td>
                                <td>{{ $request->days ?? '-' }}</td>
                                <td>
                                    @php
                                        $badge = match ($request->status) {
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            'cancelled' => 'secondary',
                                            default => 'warning',
                                        };
                                    @endphp
                                    <span
                                        class="badge bg-label-{{ $badge }}">{{ ucfirst($request->status) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No leave records found.</td>
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

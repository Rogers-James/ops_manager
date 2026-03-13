@extends('admin.layouts.layout')

@section('title', 'Admin - Attendance Reports')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">

        <h4 class="fw-bold mb-3">
            <span class="text-muted fw-light">Reports /</span> Attendance Reports
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
                        <label class="form-label">From</label>
                        <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">To</label>
                        <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary w-100">Filter</button>
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
                            <th>Date</th>
                            <th>Employee</th>
                            <th>Shift</th>
                            <th>First In</th>
                            <th>Last Out</th>
                            <th>Worked</th>
                            <th>Late</th>
                            <th>OT</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($records as $i => $record)
                            <tr>
                                <td>{{ $records->firstItem() + $i }}</td>
                                <td>{{ $record->date ? \Carbon\Carbon::parse($record->date)->format('Y-m-d') : '-' }}</td>
                                <td>{{ $record->employee->employee_code ?? '' }} -
                                    {{ $record->employee->first_name ?? '' }} {{ $record->employee->last_name ?? '' }}
                                </td>
                                <td>{{ $record->shiftType->name ?? '-' }}</td>
                                <td>{{ $record->first_in ? \Carbon\Carbon::parse($record->first_in)->format('Y-m-d H:i') : '-' }}
                                </td>
                                <td>{{ $record->last_out ? \Carbon\Carbon::parse($record->last_out)->format('Y-m-d H:i') : '-' }}
                                </td>
                                <td>{{ $record->worked_minutes ?? 0 }} min</td>
                                <td>{{ $record->late_minutes ?? 0 }} min</td>
                                <td>{{ $record->overtime_minutes ?? 0 }} min</td>
                                <td>
                                    <span class="badge bg-label-info">{{ strtoupper($record->status) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">No attendance records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $records->links() }}
            </div>
        </div>
    </div>
@endsection

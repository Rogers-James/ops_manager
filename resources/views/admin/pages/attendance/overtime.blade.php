@extends('admin.layouts.layout')

@section('title', 'Admin - Overtimes')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold mb-0">
                <span class="text-muted fw-light">Time Management /</span> Overtime
            </h4>

            <form class="d-flex gap-2" method="GET" action="{{ route('admin.attendance.overtime') }}">
                <input type="date" name="from" class="form-control form-control-sm" value="{{ $from }}">
                <input type="date" name="to" class="form-control form-control-sm" value="{{ $to }}">
                <button class="btn btn-primary btn-sm">Filter</button>
            </form>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Overtime Records</h5>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Employee</th>
                            <th>First In</th>
                            <th>Last Out</th>
                            <th>Worked</th>
                            <th>Overtime</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody class="table-border-bottom-0">
                        @forelse($records as $i => $rec)
                            <tr>
                                <td>{{ $records->firstItem() + $i }}</td>
                                <td>{{ $rec->date?->format('Y-m-d') }}</td>
                                <td>
                                    <strong>{{ $rec->employee?->employee_code }}</strong><br>
                                    <small class="text-muted">{{ $rec->employee?->first_name }}
                                        {{ $rec->employee?->last_name }}</small>
                                </td>
                                <td>{{ $rec->first_in?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td>{{ $rec->last_out?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td>
                                    @if ($rec->worked_minutes !== null)
                                        {{ intdiv($rec->worked_minutes, 60) }}h {{ $rec->worked_minutes % 60 }}m
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if ($rec->overtime_minutes)
                                        <span class="badge bg-label-success">
                                            {{ intdiv($rec->overtime_minutes, 60) }}h {{ $rec->overtime_minutes % 60 }}m
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td><span class="badge bg-label-info">{{ strtoupper($rec->status) }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">No overtime records found.</td>
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

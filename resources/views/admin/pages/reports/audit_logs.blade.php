@extends('admin.layouts.layout')

@section('title', 'Admin - Audit Logs')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">

        <h4 class="fw-bold mb-3">
            <span class="text-muted fw-light">Reports /</span> Audit Logs
        </h4>

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Request Type</th>
                            <th>Status</th>
                            <th>Payload</th>
                            <th>Created At</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($logs as $i => $log)
                            <tr>
                                <td>{{ $logs->firstItem() + $i }}</td>
                                <td>{{ $log->employee->employee_code ?? '' }} - {{ $log->employee->first_name ?? '' }}
                                    {{ $log->employee->last_name ?? '' }}</td>
                                <td>{{ $log->type->name ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-label-info">{{ strtoupper($log->status) }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $log->payload['subject'] ?? ($log->payload['reason'] ?? '-') }}
                                    </small>
                                </td>
                                <td>{{ $log->created_at ? $log->created_at->format('Y-m-d H:i') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No audit logs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
@endsection

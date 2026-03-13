@extends('admin.layouts.layout')

@section('title', 'Admin - Attendance Logs')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold mb-0">
                <span class="text-muted fw-light">Time Management /</span> Attendance Logs
            </h4>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addLogModal">
                <i class="bx bx-plus me-1"></i> Add Log
            </button>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form class="row g-2 align-items-end" method="GET" action="{{ route('admin.attendance.logs') }}">
                    <div class="col-md-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" class="form-control" value="{{ $date }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Employee</label>
                        <select name="employee_id" class="form-select">
                            <option value="">All</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}"
                                    {{ (string) $employeeId === (string) $emp->id ? 'selected' : '' }}>
                                    {{ $emp->employee_code }} - {{ $emp->first_name }} {{ $emp->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Logs</h5>

                <form method="POST" action="{{ route('admin.attendance.daily.process') }}">
                    @csrf
                    <input type="hidden" name="date" value="{{ $date }}">
                    <button class="btn btn-outline-primary btn-sm">
                        <i class="bx bx-refresh me-1"></i> Process Logs → Daily Records
                    </button>
                </form>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Log Time</th>
                            <th>Source</th>
                            <th>Device</th>
                            <th>Meta</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($logs as $i => $log)
                            <tr>
                                <td>{{ $logs->firstItem() + $i }}</td>
                                <td>
                                    <strong>{{ $log->employee?->employee_code }}</strong><br>
                                    <small class="text-muted">{{ $log->employee?->first_name }}
                                        {{ $log->employee?->last_name }}</small>
                                </td>
                                <td>{{ $log->log_time?->format('Y-m-d H:i:s') }}</td>
                                <td><span class="badge bg-label-info">{{ strtoupper($log->source) }}</span></td>
                                <td>{{ $log->device?->name ?? '-' }}</td>
                                <td><small class="text-muted">{{ $log->meta ? json_encode($log->meta) : '-' }}</small></td>
                                <td>
                                    <form method="POST" action="{{ route('admin.attendance.logs.delete', $log->id) }}"
                                        onsubmit="return confirm('Delete this log?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No logs found.</td>
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

    {{-- Add Log Modal --}}
    <div class="modal fade" id="addLogModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.attendance.logs.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Attendance Log</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Employee</label>
                        <select name="employee_id" class="form-select" required>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}">
                                    {{ $emp->employee_code }} - {{ $emp->first_name }} {{ $emp->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Log Time</label>
                        <input type="datetime-local" name="log_time" class="form-control" required
                            value="{{ old('log_time') }}">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Source</label>
                        <select name="source" class="form-select" required>
                            @foreach (['manual', 'device', 'employee', 'import'] as $s)
                                <option value="{{ $s }}">{{ strtoupper($s) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Device (optional)</label>
                        <select name="attendance_device_id" class="form-select">
                            <option value="">-- None --</option>
                            @foreach ($devices as $d)
                                <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->type }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Meta (optional JSON)</label>
                        <input type="text" class="form-control" name="meta[type]"
                            placeholder='Example: IN / OUT (stores as meta.type)'>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger mt-2 mb-0">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if ($errors->any())
                new bootstrap.Modal(document.getElementById('addLogModal')).show();
            @endif
        });
    </script>

@endsection

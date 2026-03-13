@extends('admin.layouts.layout')

@section('title', 'Admin - Attendance')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold mb-0">
                <span class="text-muted fw-light">Time Management /</span> Daily Attendance
            </h4>

            <form class="d-flex gap-2" method="GET" action="{{ route('admin.attendance.daily') }}">
                <input type="date" name="date" class="form-control form-control-sm" value="{{ $date }}">
                <button class="btn btn-primary btn-sm">Filter</button>
            </form>
        </div>

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Attendance for: <span class="text-primary">{{ $date }}</span></h5>

                <form method="POST" action="{{ route('admin.attendance.daily.process') }}">
                    @csrf
                    <input type="hidden" name="date" value="{{ $date }}">
                    <button type="submit" class="btn btn-outline-primary btn-sm">
                        <i class="bx bx-refresh me-1"></i> Process Logs for Date
                    </button>
                </form>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Status</th>
                            <th>First In</th>
                            <th>Last Out</th>
                            <th>Worked</th>
                            <th>OT</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($employees as $i => $emp)
                            @php
                                $rec = $records[$emp->id] ?? null;
                            @endphp
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    <strong>{{ $emp->employee_code }}</strong><br>
                                    <small class="text-muted">{{ $emp->first_name }} {{ $emp->last_name }}</small>
                                </td>

                                <td>
                                    @php
                                        $st = $rec->status ?? null;
                                        $badge = match ($st) {
                                            'present' => 'success',
                                            'absent' => 'danger',
                                            'late' => 'warning',
                                            'half_day' => 'info',
                                            'leave' => 'primary',
                                            'holiday' => 'secondary',
                                            'weekend' => 'secondary',
                                            'wfh' => 'info',
                                            'on_duty' => 'info',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    @if ($rec)
                                        <span
                                            class="badge bg-label-{{ $badge }}">{{ strtoupper($rec->status) }}</span>
                                    @else
                                        <span class="badge bg-label-secondary">NOT MARKED</span>
                                    @endif
                                </td>

                                <td>{{ $rec?->first_in ? $rec->first_in->format('Y-m-d H:i') : '-' }}</td>
                                <td>{{ $rec?->last_out ? $rec->last_out->format('Y-m-d H:i') : '-' }}</td>

                                <td>
                                    @if ($rec && $rec->worked_minutes !== null)
                                        {{ intdiv($rec->worked_minutes, 60) }}h {{ $rec->worked_minutes % 60 }}m
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    @if ($rec && $rec->overtime_minutes)
                                        {{ intdiv($rec->overtime_minutes, 60) }}h {{ $rec->overtime_minutes % 60 }}m
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#markAttendanceModal" data-employee-id="{{ $emp->id }}"
                                        data-employee-name="{{ $emp->employee_code }} - {{ $emp->first_name }} {{ $emp->last_name }}"
                                        data-date="{{ $date }}" data-status="{{ $rec->status ?? 'present' }}"
                                        data-first-in="{{ $rec?->first_in?->format('Y-m-d\TH:i') }}"
                                        data-last-out="{{ $rec?->last_out?->format('Y-m-d\TH:i') }}">
                                        <i class="bx bx-edit-alt me-1"></i> {{ $rec ? 'Edit' : 'Mark' }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">No employees found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer text-muted">
                Tip: Use “Process Logs for Date” after device/import logs to auto-fill daily records.
            </div>
        </div>
    </div>

    {{-- Mark/Edit Modal --}}
    <div class="modal fade" id="markAttendanceModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.attendance.daily.mark') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Mark Attendance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="employee_id" id="att_employee_id">
                    <div class="mb-2">
                        <label class="form-label">Employee</label>
                        <input type="text" class="form-control" id="att_employee_name" readonly>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" class="form-control" id="att_date" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Status</label>
                        @php
                            $statuses = [
                                'present',
                                'absent',
                                'late',
                                'half_day',
                                'leave',
                                'holiday',
                                'weekend',
                                'wfh',
                                'on_duty',
                            ];
                        @endphp
                        <select name="status" class="form-select" id="att_status" required>
                            @foreach ($statuses as $s)
                                <option value="{{ $s }}">{{ strtoupper($s) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">First In (optional)</label>
                            <input type="datetime-local" name="first_in" class="form-control" id="att_first_in">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Last Out (optional)</label>
                            <input type="datetime-local" name="last_out" class="form-control" id="att_last_out">
                        </div>
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

    {{-- Modal JS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('markAttendanceModal');

            modal.addEventListener('show.bs.modal', function(event) {
                const btn = event.relatedTarget;

                document.getElementById('att_employee_id').value = btn.getAttribute('data-employee-id');
                document.getElementById('att_employee_name').value = btn.getAttribute('data-employee-name');
                document.getElementById('att_date').value = btn.getAttribute('data-date');

                const status = btn.getAttribute('data-status') || 'present';
                document.getElementById('att_status').value = status;

                document.getElementById('att_first_in').value = btn.getAttribute('data-first-in') || '';
                document.getElementById('att_last_out').value = btn.getAttribute('data-last-out') || '';
            });

            @if ($errors->any())
                new bootstrap.Modal(modal).show();
            @endif
        });
    </script>

@endsection

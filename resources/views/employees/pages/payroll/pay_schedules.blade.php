@extends('admin.layouts.layout')

@section('title', 'Admin - Pay Schedules')

@section('main-content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Payroll Setup /</span> Pay Schedules</h4>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSchedule">
                <i class="bx bx-plus me-1"></i> Add Schedule
            </button>
        </div>

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Frequency</th>
                            <th>Pay Day</th>
                            <th>Week Day</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($paySchedules as $i => $s)
                            <tr>
                                <td>{{ $paySchedules->firstItem() + $i }}</td>
                                <td><strong>{{ $s->name }}</strong></td>
                                <td><span class="badge bg-label-info">{{ strtoupper($s->frequency) }}</span></td>
                                <td>{{ $s->pay_day ?? '-' }}</td>
                                <td>{{ $s->week_day ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-label-{{ $s->is_active ? 'success' : 'secondary' }}">
                                        {{ $s->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="d-flex gap-1">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#editSchedule-{{ $s->id }}">
                                        <i class="bx bx-edit-alt"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.pay_schedules.destroy', $s) }}"
                                        onsubmit="return confirm('Delete this schedule?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Edit Modal --}}
                            <div class="modal fade" id="editSchedule-{{ $s->id }}" data-bs-backdrop="static"
                                tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form class="modal-content" method="POST"
                                        action="{{ route('admin.pay_schedules.update', $s) }}">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="__modal" value="editSchedule-{{ $s->id }}">

                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Pay Schedule</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="row g-2">
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">Name</label>
                                                    <input class="form-control" name="name" required
                                                        value="{{ old('name', $s->name) }}">
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">Frequency</label>
                                                    <select name="frequency" class="form-select" required>
                                                        @foreach (['monthly', 'weekly', 'biweekly', 'semimonthly'] as $f)
                                                            <option value="{{ $f }}"
                                                                {{ old('frequency', $s->frequency) === $f ? 'selected' : '' }}>
                                                                {{ ucwords($f) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row g-2">
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">Pay Day (Monthly)</label>
                                                    <input type="number" min="1" max="31" class="form-control"
                                                        name="pay_day" value="{{ old('pay_day', $s->pay_day) }}">
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">Week Day (Weekly 0=Sun..6=Sat)</label>
                                                    <input type="number" min="0" max="6" class="form-control"
                                                        name="week_day" value="{{ old('week_day', $s->week_day) }}">
                                                </div>
                                            </div>

                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="is_active"
                                                    value="1"
                                                    {{ old('is_active', (int) $s->is_active) ? 'checked' : '' }}>
                                                <label class="form-check-label">Active</label>
                                            </div>

                                            @if ($errors->any() && old('__modal') === "editSchedule-$s->id")
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
                                            <button type="button" class="btn btn-outline-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button class="btn btn-primary">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No pay schedules found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">{{ $paySchedules->links() }}</div>
        </div>
    </div>

    {{-- Add Modal --}}
    <div class="modal fade" id="addSchedule" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" method="POST" action="{{ route('admin.pay_schedules.store') }}">
                @csrf
                <input type="hidden" name="__modal" value="addSchedule">

                <div class="modal-header">
                    <h5 class="modal-title">Add Pay Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Name</label>
                            <input class="form-control" name="name" required value="{{ old('name') }}">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Frequency</label>
                            <select name="frequency" class="form-select" required>
                                @foreach (['monthly', 'weekly', 'biweekly', 'semimonthly'] as $f)
                                    <option value="{{ $f }}"
                                        {{ old('frequency', 'monthly') === $f ? 'selected' : '' }}>
                                        {{ ucwords($f) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Pay Day (Monthly)</label>
                            <input type="number" min="1" max="31" class="form-control" name="pay_day"
                                value="{{ old('pay_day') }}">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Week Day (Weekly 0=Sun..6=Sat)</label>
                            <input type="number" min="0" max="6" class="form-control" name="week_day"
                                value="{{ old('week_day') }}">
                        </div>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                            {{ old('is_active', 1) ? 'checked' : '' }}>
                        <label class="form-check-label">Active</label>
                    </div>

                    @if ($errors->any() && old('__modal') === 'addSchedule')
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
            @if ($errors->any() && old('__modal'))
                new bootstrap.Modal(document.getElementById(@json(old('__modal')))).show();
            @endif
        });
    </script>
@endsection

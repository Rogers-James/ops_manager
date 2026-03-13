@extends('admin.layouts.layout')

@section('title', 'Admin - Leave Policies')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Leave Management /</span> Leave Policies</h4>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPolicy">
                <i class="bx bx-plus me-1"></i> Add Policy
            </button>
        </div>

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Policy</th>
                            <th>Leave Type</th>
                            <th>Quota</th>
                            <th>Accrual</th>
                            <th>Negative</th>
                            <th>Notice</th>
                            <th>Max Consecutive</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($policies as $i => $p)
                            <tr>
                                <td>{{ $policies->firstItem() + $i }}</td>
                                <td><strong>{{ $p->name }}</strong></td>
                                <td>{{ $p->leaveType->name ?? '-' }}</td>
                                <td><span class="badge bg-label-primary">{{ $p->yearly_quota }}</span></td>
                                <td>
                                    <span class="badge bg-label-info">
                                        {{ strtoupper($p->accrual_method) }}
                                        @if ($p->accrual_rate)
                                            ({{ $p->accrual_rate }})
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    @if ($p->allow_negative)
                                        <span class="badge bg-label-warning">Yes ({{ $p->max_negative ?? 0 }})</span>
                                    @else
                                        <span class="badge bg-label-secondary">No</span>
                                    @endif
                                </td>
                                <td>{{ $p->min_notice_days ?? 0 }} day(s)</td>
                                <td>{{ $p->max_consecutive_days ?? '-' }}</td>
                                <td class="d-flex gap-1">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#editPolicy-{{ $p->id }}">
                                        <i class="bx bx-edit-alt"></i>
                                    </button>

                                    <form method="POST" action="{{ route('admin.leave_policies.destroy', $p) }}"
                                        onsubmit="return confirm('Delete this policy?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Edit Policy Modal --}}
                            <div class="modal fade" id="editPolicy-{{ $p->id }}" data-bs-backdrop="static"
                                tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form class="modal-content" method="POST"
                                        action="{{ route('admin.leave_policies.update', $p) }}">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="__modal" value="editPolicy-{{ $p->id }}">

                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Leave Policy</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="row g-2">
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">Policy Name</label>
                                                    <input class="form-control" name="name" required
                                                        value="{{ old('name', $p->name) }}">
                                                </div>

                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">Leave Type</label>
                                                    <select name="leave_type_id" class="form-select" required>
                                                        @foreach ($leaveTypes as $t)
                                                            <option value="{{ $t->id }}"
                                                                {{ (string) old('leave_type_id', $p->leave_type_id) === (string) $t->id ? 'selected' : '' }}>
                                                                {{ $t->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row g-2">
                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Yearly Quota</label>
                                                    <input type="number" class="form-control" name="yearly_quota"
                                                        min="0" max="366"
                                                        value="{{ old('yearly_quota', $p->yearly_quota) }}" required>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Accrual Method</label>
                                                    <select name="accrual_method" class="form-select" required>
                                                        @foreach (['none' => 'None', 'monthly' => 'Monthly', 'yearly' => 'Yearly'] as $k => $v)
                                                            <option value="{{ $k }}"
                                                                {{ old('accrual_method', $p->accrual_method) === $k ? 'selected' : '' }}>
                                                                {{ $v }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Accrual Rate</label>
                                                    <input type="number" step="0.01" class="form-control"
                                                        name="accrual_rate"
                                                        value="{{ old('accrual_rate', $p->accrual_rate) }}"
                                                        placeholder="e.g. 1.5">
                                                </div>
                                            </div>

                                            <div class="row g-2">
                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Min Notice Days</label>
                                                    <input type="number" class="form-control" name="min_notice_days"
                                                        min="0" max="365"
                                                        value="{{ old('min_notice_days', $p->min_notice_days) }}">
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Max Consecutive Days</label>
                                                    <input type="number" class="form-control" name="max_consecutive_days"
                                                        min="0" max="366"
                                                        value="{{ old('max_consecutive_days', $p->max_consecutive_days) }}">
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Negative Leave</label>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="allow_negative" value="1"
                                                            {{ old('allow_negative', (int) $p->allow_negative) ? 'checked' : '' }}>
                                                        <label class="form-check-label">Allow</label>
                                                    </div>

                                                    <input type="number" class="form-control mt-2" name="max_negative"
                                                        min="0" max="366"
                                                        value="{{ old('max_negative', $p->max_negative) }}"
                                                        placeholder="Max negative">
                                                </div>
                                            </div>

                                            @if ($errors->any() && old('__modal') === "editPolicy-$p->id")
                                                <div class="alert alert-danger mb-0">
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
                                <td colspan="9" class="text-center py-4">No policies found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">{{ $policies->links() }}</div>
        </div>
    </div>

    {{-- Add Policy Modal --}}
    <div class="modal fade" id="addPolicy" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" method="POST" action="{{ route('admin.leave_policies.store') }}">
                @csrf
                <input type="hidden" name="__modal" value="addPolicy">

                <div class="modal-header">
                    <h5 class="modal-title">Add Leave Policy</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Policy Name</label>
                            <input class="form-control" name="name" required value="{{ old('name') }}">
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">Leave Type</label>
                            <select name="leave_type_id" class="form-select" required>
                                <option value="">-- Select --</option>
                                @foreach ($leaveTypes as $t)
                                    <option value="{{ $t->id }}"
                                        {{ (string) old('leave_type_id') === (string) $t->id ? 'selected' : '' }}>
                                        {{ $t->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Yearly Quota</label>
                            <input type="number" class="form-control" name="yearly_quota" min="0"
                                max="366" value="{{ old('yearly_quota', 0) }}" required>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="form-label">Accrual Method</label>
                            <select name="accrual_method" class="form-select" required>
                                @foreach (['none' => 'None', 'monthly' => 'Monthly', 'yearly' => 'Yearly'] as $k => $v)
                                    <option value="{{ $k }}"
                                        {{ old('accrual_method', 'none') === $k ? 'selected' : '' }}>
                                        {{ $v }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="form-label">Accrual Rate</label>
                            <input type="number" step="0.01" class="form-control" name="accrual_rate"
                                value="{{ old('accrual_rate') }}" placeholder="e.g. 1.5">
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Min Notice Days</label>
                            <input type="number" class="form-control" name="min_notice_days" min="0"
                                max="365" value="{{ old('min_notice_days', 0) }}">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="form-label">Max Consecutive Days</label>
                            <input type="number" class="form-control" name="max_consecutive_days" min="0"
                                max="366" value="{{ old('max_consecutive_days') }}">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="form-label">Negative Leave</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="allow_negative" value="1"
                                    {{ old('allow_negative') ? 'checked' : '' }}>
                                <label class="form-check-label">Allow</label>
                            </div>

                            <input type="number" class="form-control mt-2" name="max_negative" min="0"
                                max="366" value="{{ old('max_negative') }}" placeholder="Max negative">
                        </div>
                    </div>

                    @if ($errors->any() && old('__modal') === 'addPolicy')
                        <div class="alert alert-danger mb-0">
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

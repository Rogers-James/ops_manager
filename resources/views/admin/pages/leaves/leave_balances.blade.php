@extends('admin.layouts.layout')

@section('title', 'Admin - Leave Balances')

@section('main-content')


    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Leave Management /</span> Leave Balances</h4>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#adjustBalance">
                <i class="bx bx-plus me-1"></i> Adjust Balance
            </button>
        </div>

        {{-- Filters --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label">Employee</label>
                        <select name="employee_id" class="form-select">
                            <option value="">All</option>
                            @foreach ($employees as $e)
                                <option value="{{ $e->id }}" {{ request('employee_id') == $e->id ? 'selected' : '' }}>
                                    {{ $e->employee_code }} - {{ $e->first_name }} {{ $e->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Leave Type</label>
                        <select name="leave_type_id" class="form-select">
                            <option value="">All</option>
                            @foreach ($leaveTypes as $t)
                                <option value="{{ $t->id }}"
                                    {{ request('leave_type_id') == $t->id ? 'selected' : '' }}>
                                    {{ $t->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Leave Type</th>
                            <th>Balance</th>
                            <th>Used</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($balances as $i => $b)
                            <tr>
                                <td>{{ $balances->firstItem() + $i }}</td>
                                <td>
                                    <strong>{{ $b->employee->employee_code ?? '' }}</strong> -
                                    {{ $b->employee->first_name ?? '' }} {{ $b->employee->last_name ?? '' }}
                                </td>
                                <td>{{ $b->leaveType->name ?? '-' }}</td>
                                <td><span class="badge bg-label-primary">{{ $b->balance ?? 0 }}</span></td>
                                <td><span class="badge bg-label-info">{{ $b->used ?? 0 }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">No balances found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">{{ $balances->links() }}</div>
        </div>
    </div>

    {{-- Adjust Balance Modal --}}
    <div class="modal fade" id="adjustBalance" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" method="POST" action="{{ route('admin.leave_balances.adjust') }}">
                @csrf
                <input type="hidden" name="__modal" value="adjustBalance">

                <div class="modal-header">
                    <h5 class="modal-title">Adjust Leave Balance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Employee</label>
                            <select name="employee_id" class="form-select" required>
                                <option value="">-- Select --</option>
                                @foreach ($employees as $e)
                                    <option value="{{ $e->id }}"
                                        {{ (string) old('employee_id') === (string) $e->id ? 'selected' : '' }}>
                                        {{ $e->employee_code }} - {{ $e->first_name }} {{ $e->last_name }}
                                    </option>
                                @endforeach
                            </select>
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
                            <label class="form-label">Amount (+ / -)</label>
                            <input type="number" class="form-control" name="amount" required min="-366" max="366"
                                value="{{ old('amount') }}" placeholder="e.g. 10 or -2">
                        </div>
                        <div class="col-md-8 mb-2">
                            <label class="form-label">Note (optional)</label>
                            <input class="form-control" name="note" value="{{ old('note') }}"
                                placeholder="Reason for adjustment">
                        </div>
                    </div>

                    @if ($errors->any() && old('__modal') === 'adjustBalance')
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

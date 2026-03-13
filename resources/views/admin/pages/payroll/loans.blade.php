@extends('admin.layouts.layout')

@section('title', 'Admin - Loans & Advances')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Payroll Processing /</span> Loans & Advances</h4>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addLoan">
                <i class="bx bx-plus me-1"></i> Add Loan/Advance
            </button>
        </div>

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Installment</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loans as $i => $l)
                            <tr>
                                <td>{{ $loans->firstItem() + $i }}</td>
                                <td>
                                    <strong>{{ $l->employee->employee_code ?? '' }}</strong>
                                    - {{ $l->employee->first_name ?? '' }} {{ $l->employee->last_name ?? '' }}
                                </td>
                                <td><span class="badge bg-label-info">{{ strtoupper($l->type) }}</span></td>
                                <td>{{ number_format($l->amount, 2) }}</td>
                                <td>{{ $l->installment_amount ? number_format($l->installment_amount, 2) : '-' }}</td>
                                <td><span
                                        class="badge bg-label-{{ $l->status === 'active' ? 'success' : 'secondary' }}">{{ strtoupper($l->status) }}</span>
                                </td>
                                <td class="d-flex gap-1">
                                    <form method="POST" action="{{ route('admin.loans.destroy', $l) }}"
                                        onsubmit="return confirm('Delete this record?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No loans found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $loans->links() }}</div>
        </div>
    </div>

    {{-- Add Loan Modal --}}
    <div class="modal fade" id="addLoan" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" method="POST" action="{{ route('admin.loans.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Loan / Advance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Employee</label>
                            <select name="employee_id" class="form-select" required>
                                <option value="">-- Select --</option>
                                @foreach ($employees as $e)
                                    <option value="{{ $e->id }}">{{ $e->employee_code }} - {{ $e->first_name }}
                                        {{ $e->last_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select" required>
                                <option value="loan">Loan</option>
                                <option value="advance">Advance</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Amount</label>
                            <input type="number" step="0.01" class="form-control" name="amount" required>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Installment Amount</label>
                            <input type="number" step="0.01" class="form-control" name="installment_amount">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Start Month</label>
                            <input type="date" class="form-control" name="start_month">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Notes</label>
                        <input class="form-control" name="notes">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection

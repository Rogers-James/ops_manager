@extends('admin.layouts.layout')

@section('title', 'Admin - Payroll Reports')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">

        <h4 class="fw-bold mb-3">
            <span class="text-muted fw-light">Reports /</span> Payroll Reports
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

                    <div class="col-md-4">
                        <label class="form-label">Payroll Run</label>
                        <select name="payroll_run_id" class="form-select">
                            <option value="">All</option>
                            @foreach ($runs as $run)
                                <option value="{{ $run->id }}"
                                    {{ request('payroll_run_id') == $run->id ? 'selected' : '' }}>
                                    Run #{{ $run->id }}
                                    ({{ \Carbon\Carbon::parse($run->period_start)->format('Y-m-d') }} →
                                    {{ \Carbon\Carbon::parse($run->period_end)->format('Y-m-d') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary w-100">Filter</button>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <a href="{{ route('admin.reports.payroll') }}" class="btn btn-outline-secondary w-100">Reset</a>
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
                            <th>Run</th>
                            <th>Gross</th>
                            <th>Deductions</th>
                            <th>Net</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($items as $i => $item)
                            <tr>
                                <td>{{ $items->firstItem() + $i }}</td>
                                <td>{{ $item->employee->employee_code ?? '' }} - {{ $item->employee->first_name ?? '' }}
                                    {{ $item->employee->last_name ?? '' }}</td>
                                <td>
                                    #{{ $item->payrollRun->id ?? '-' }}
                                    @if ($item->payrollRun)
                                        <br>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($item->payrollRun->period_start)->format('Y-m-d') }}
                                            →
                                            {{ \Carbon\Carbon::parse($item->payrollRun->period_end)->format('Y-m-d') }}
                                        </small>
                                    @endif
                                </td>
                                <td>{{ number_format($item->gross_amount ?? 0, 2) }}</td>
                                <td>{{ number_format($item->deduction_amount ?? 0, 2) }}</td>
                                <td><strong>{{ number_format($item->net_amount ?? 0, 2) }}</strong></td>
                                <td>
                                    <span class="badge bg-label-info">{{ strtoupper($item->status ?? 'draft') }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No payroll report data found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $items->links() }}
            </div>
        </div>
    </div>
@endsection

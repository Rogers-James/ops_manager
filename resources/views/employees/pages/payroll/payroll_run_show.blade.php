@extends('admin.layouts.layout')

@section('title', 'Admin - Payroll Run Details')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h4 class="fw-bold mb-0">Payroll Run #{{ $payrollRun->id }}</h4>

            <form method="POST" action="{{ route('admin.payroll_runs.status', $payrollRun) }}" class="d-flex gap-2">
                @csrf @method('PUT')
                <select name="status" class="form-select form-select-sm" style="width: 160px;">
                    @foreach (['draft', 'processed', 'approved', 'paid', 'cancelled'] as $s)
                        <option value="{{ $s }}" {{ $payrollRun->status === $s ? 'selected' : '' }}>
                            {{ strtoupper($s) }}
                        </option>
                    @endforeach
                </select>
                <button class="btn btn-primary btn-sm">Update</button>
            </form>
        </div>

        <div class="text-muted mb-3">
            Schedule: <strong>{{ $payrollRun->paySchedule->name ?? '-' }}</strong> |
            Period: <strong>{{ \Carbon\Carbon::parse($payrollRun->period_start)->format('Y-m-d') }}</strong> →
            <strong>{{ \Carbon\Carbon::parse($payrollRun->period_end)->format('Y-m-d') }}</strong>
        </div>

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Gross</th>
                            <th>Deductions</th>
                            <th>Net</th>
                            <th>Payslip</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payrollRun->items as $it)
                            <tr>
                                <td>
                                    <strong>{{ $it->employee->employee_code ?? '' }}</strong>
                                    - {{ $it->employee->first_name ?? '' }} {{ $it->employee->last_name ?? '' }}
                                </td>
                                <td>{{ number_format($it->gross_amount ?? 0, 2) }}</td>
                                <td>{{ number_format($it->deduction_amount ?? 0, 2) }}</td>
                                <td><strong>{{ number_format($it->net_amount ?? 0, 2) }}</strong></td>
                                <td>
                                    <a class="btn btn-sm btn-outline-primary"
                                        href="{{ route('admin.payslips.show', [$payrollRun, $it->employee_id]) }}">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">No payroll items found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

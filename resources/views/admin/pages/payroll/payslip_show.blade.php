@extends('admin.layouts.layout')

@section('title', 'Admin - Payslip')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold mb-2"><span class="text-muted fw-light">Payslip /</span>
            {{ $item->employee->employee_code }} - {{ $item->employee->first_name }} {{ $item->employee->last_name }}
        </h4>

        <div class="card">
            <div class="card-body">
                <p class="mb-1"><strong>Payroll Run:</strong> #{{ $item->payroll_run_id }}</p>
                <p class="mb-1"><strong>Period:</strong>
                    {{ \Carbon\Carbon::parse($item->payrollRun->period_start)->format('Y-m-d') }} →
                    {{ \Carbon\Carbon::parse($item->payrollRun->period_end)->format('Y-m-d') }}
                </p>

                <hr>

                <div class="row">
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <div class="text-muted">Gross</div>
                            <h4 class="mb-0">{{ number_format($item->gross_amount ?? 0, 2) }}</h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <div class="text-muted">Deductions</div>
                            <h4 class="mb-0">{{ number_format($item->deduction_amount ?? 0, 2) }}</h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <div class="text-muted">Net Pay</div>
                            <h4 class="mb-0">{{ number_format($item->net_amount ?? 0, 2) }}</h4>
                        </div>
                    </div>
                </div>

                <hr>

                <a href="{{ route('admin.payroll_runs.show', $item->payroll_run_id) }}" class="btn btn-outline-secondary">
                    Back to Run
                </a>
            </div>
        </div>
    </div>
@endsection

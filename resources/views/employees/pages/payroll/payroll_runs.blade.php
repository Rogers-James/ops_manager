@extends('admin.layouts.layout')

@section('title', 'Admin - Payroll Runs')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Payroll Processing /</span> Payroll Runs</h4>
            <a href="{{ route('admin.payroll.run.create') }}" class="btn btn-primary btn-sm">
                <i class="bx bx-plus me-1"></i> Run Payroll
            </a>
        </div>

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Schedule</th>
                            <th>Period</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Open</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($runs as $i => $r)
                            <tr>
                                <td>{{ $runs->firstItem() + $i }}</td>
                                <td>{{ $r->paySchedule->name ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($r->period_start)->format('Y-m-d') }} →
                                    {{ \Carbon\Carbon::parse($r->period_end)->format('Y-m-d') }}</td>
                                <td><span class="badge bg-label-info">{{ strtoupper($r->status) }}</span></td>
                                <td>{{ $r->created_at?->format('Y-m-d') }}</td>
                                <td>
                                    <a class="btn btn-sm btn-primary" href="{{ route('admin.payroll_runs.show', $r) }}">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No payroll runs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $runs->links() }}</div>
        </div>
    </div>
@endsection

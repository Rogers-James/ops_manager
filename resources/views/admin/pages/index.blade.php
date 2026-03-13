@extends('admin.layouts.layout')

@section('title', 'Admin - Dashboard')

@section('main-content')

    @php
        $attendanceTrendLabels = $attendanceTrend
            ->map(fn($item) => \Carbon\Carbon::parse($item->date)->format('d M'))
            ->values();
        $attendanceTrendPresent = $attendanceTrend->pluck('present_count')->map(fn($v) => (int) $v)->values();
        $attendanceTrendLeave = $attendanceTrend->pluck('leave_count')->map(fn($v) => (int) $v)->values();
        $attendanceTrendAbsent = $attendanceTrend->pluck('absent_count')->map(fn($v) => (int) $v)->values();
    @endphp

    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h4 class="mb-1">Welcome back, {{ $admin->name ?? 'Admin' }}</h4>
                            <p class="mb-0 text-muted">
                                Here is your HR overview for {{ \Carbon\Carbon::parse($today)->format('d M Y') }}.
                            </p>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('admin.attendance.daily') }}" class="btn btn-primary">View Attendance</a>
                            <a href="{{ route('admin.leave_requests.index') }}" class="btn btn-outline-primary">View Leave
                                Requests</a>
                            <a href="{{ route('admin.payroll_runs.index') }}" class="btn btn-outline-dark">Payroll Runs</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI cards --}}
        <div class="row">
            <div class="col-lg-2 col-md-4 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <span class="fw-semibold d-block mb-1">Total Employees</span>
                        <h3 class="card-title mb-2">{{ $totalEmployees }}</h3>
                        <small class="text-muted">Active: {{ $activeEmployees }}</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <span class="fw-semibold d-block mb-1">Present Today</span>
                        <h3 class="card-title mb-2">{{ $presentToday }}</h3>
                        <small class="text-muted">Attendance snapshot</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <span class="fw-semibold d-block mb-1">On Leave</span>
                        <h3 class="card-title mb-2">{{ $onLeaveToday }}</h3>
                        <small class="text-muted">Today</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <span class="fw-semibold d-block mb-1">Late Arrivals</span>
                        <h3 class="card-title mb-2">{{ $lateToday }}</h3>
                        <small class="text-muted">Today</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <span class="fw-semibold d-block mb-1">Pending Approvals</span>
                        <h3 class="card-title mb-2">{{ $pendingApprovals }}</h3>
                        <small class="text-muted">All modules</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <span class="fw-semibold d-block mb-1">Open Exits</span>
                        <h3 class="card-title mb-2">{{ $openExits }}</h3>
                        <small class="text-muted">Submitted resignations</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Attendance trend --}}
            <div class="col-lg-8 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Attendance Trend (Last 7 Days)</h5>
                    </div>
                    <div class="card-body">
                        <div id="attendanceTrendChart"></div>
                    </div>
                </div>
            </div>

            {{-- Payroll snapshot --}}
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Payroll Snapshot</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted d-block">Current Status</small>
                            <h4 class="mb-0 text-capitalize">{{ str_replace('_', ' ', $payrollStatus) }}</h4>
                        </div>

                        @if ($currentPayrollRun)
                            <div class="mb-3">
                                <small class="text-muted d-block">Period</small>
                                <div>
                                    {{ $currentPayrollRun->period_start->format('d M Y') }}
                                    -
                                    {{ $currentPayrollRun->period_end->format('d M Y') }}
                                </div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <small class="text-muted d-block">Net Payroll This Month</small>
                            <h4 class="mb-0">{{ number_format($thisMonthPayrollNet, 2) }}</h4>
                        </div>

                        <a href="{{ route('admin.payroll_runs.index') }}" class="btn btn-outline-primary btn-sm">Open
                            Payroll</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Today's attendance table --}}
            <div class="col-lg-8 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Today Attendance</h5>
                        <a href="{{ route('admin.attendance.daily') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Shift</th>
                                    <th>First In</th>
                                    <th>Last Out</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($todayAttendance as $row)
                                    <tr>
                                        <td>
                                            {{ $row->employee->first_name ?? '' }} {{ $row->employee->last_name ?? '' }}
                                        </td>
                                        <td>{{ $row->employee->employment->department->name ?? '-' }}</td>
                                        <td>{{ $row->shiftType->name ?? '-' }}</td>
                                        <td>{{ $row->first_in ? $row->first_in->format('h:i A') : '-' }}</td>
                                        <td>{{ $row->last_out ? $row->last_out->format('h:i A') : '-' }}</td>
                                        <td>
                                            <span class="badge bg-label-primary text-capitalize">{{ $row->status }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No attendance records for today.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Pending approvals --}}
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Pending Approvals</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted">Leave Requests Pending</small>
                            <h4 class="mb-0">{{ $pendingLeaveRequests }}</h4>
                        </div>

                        <ul class="list-group list-group-flush">
                            @forelse($latestApprovals as $approval)
                                <li class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="fw-semibold">{{ class_basename($approval->request_type) }}</div>
                                            <small class="text-muted">Step {{ $approval->current_step }}</small>
                                        </div>
                                        <span class="badge bg-label-warning">{{ ucfirst($approval->status) }}</span>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item px-0 text-muted">No pending approvals.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Setup alerts --}}
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Setup Alerts</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Without Salary Structure</span>
                            <span class="fw-bold">{{ $employeesWithoutSalary }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Without Bank Account</span>
                            <span class="fw-bold">{{ $employeesWithoutBank }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Without Shift Assignment</span>
                            <span class="fw-bold">{{ $employeesWithoutShift }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Without Leave Policy</span>
                            <span class="fw-bold">{{ $employeesWithoutLeavePolicy }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Department headcount --}}
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Department Headcount</h5>
                    </div>
                    <div class="card-body">
                        @forelse($departmentHeadcount as $dept)
                            <div class="d-flex justify-content-between mb-3">
                                <span>{{ $dept->name }}</span>
                                <span class="fw-bold">{{ $dept->total }}</span>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No department data found.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Recent activity --}}
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @forelse($recentActivities as $activity)
                                <li class="list-group-item px-0">
                                    <div class="fw-semibold">{{ ucfirst($activity->event) }}</div>
                                    <small class="text-muted">
                                        {{ $activity->user->name ?? 'System' }}
                                        · {{ class_basename($activity->auditable_type) }}
                                        · {{ $activity->created_at->diffForHumans() }}
                                    </small>
                                </li>
                            @empty
                                <li class="list-group-item px-0 text-muted">No recent activity found.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const attendanceTrendChartEl = document.querySelector('#attendanceTrendChart');

            if (attendanceTrendChartEl) {
                const attendanceTrendChart = new ApexCharts(attendanceTrendChartEl, {
                    chart: {
                        type: 'line',
                        height: 320,
                        toolbar: {
                            show: false
                        }
                    },
                    stroke: {
                        width: 3,
                        curve: 'smooth'
                    },
                    series: [{
                            name: 'Present',
                            data: @json($attendanceTrendPresent)
                        },
                        {
                            name: 'Leave',
                            data: @json($attendanceTrendLeave)
                        },
                        {
                            name: 'Absent',
                            data: @json($attendanceTrendAbsent)
                        }
                    ],
                    xaxis: {
                        categories: @json($attendanceTrendLabels)
                    },
                    yaxis: {
                        min: 0
                    },
                    dataLabels: {
                        enabled: false
                    },
                    legend: {
                        position: 'top'
                    }
                });

                attendanceTrendChart.render();
            }
        });
    </script>

@endsection
